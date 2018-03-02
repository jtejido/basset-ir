<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\PostingStatistics;
use Basset\Statistics\EntryStatistics;
use Basset\Ranking\ProbabilisticDistribution\ProbabilisticDistributionInterface;
use Basset\Ranking\IBLambda\IBLambdaInterface;
use Basset\Ranking\Normalization\NormalizationInterface;


/**
 * Provides a framework for the family of information-based models, as described
 * in St&eacute;phane Clinchant and Eric Gaussier. 2010. Information-based
 * models for ad hoc IR. In Proceeding of the 33rd international ACM SIGIR
 * conference on Research and development in information retrieval (SIGIR '10).
 * ACM, New York, NY, USA, 234-241.
 *
 * Information-based models are obtained by instantiating the three components of the framework: 
 * selecting a ProbabilisticDistribution, selecting the lambda parameter and applying the tf normalisation.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class IBRanking extends AbstractRanking
{

    protected $probdist;

    protected $lambda;

    protected $normalization;

    protected $collectionstats;

    public function __construct(ProbabilisticDistributionInterface $probdist, IBLambdaInterface $lambda, NormalizationInterface $normalization, CollectionSet $set)
    {
        parent::__construct($set);
        $this->probdist    = $probdist;
        $this->lambda    = $lambda;
        $this->normalization    = $normalization;
        $this->collectionstats = new CollectionStatistics($this->set);

        if ($this->probdist == null || $this->lambda == null || $this->normalization == null) {
            throw new \Exception("Null Parameters not allowed.");
        }

    }

    /**
     * Returns result ordered by rank.
     *
     * @param  DocumentInterface $q
     * @return array
     */
    public function search(DocumentInterface $q)
    {

        $score = array();
        $term_stats = new PostingStatistics($q); //get query index
        $terms = array_keys($term_stats->getDocument());

        foreach($this->set as $class => $doc) {
            $score[$class] = isset($score[$class]) ? $score[$class] : 0;
            $posting_stats = new PostingStatistics($doc); //get doc index
            $docLength = $posting_stats->getDocumentLength();
            foreach ($terms as $term){
                $entrystats = new EntryStatistics($this->collectionstats, $term);
                $tf = $posting_stats->getTf($term);
                $keyFrequency = $term_stats->getTf($term);
                if($tf > 0) {
                    if($this->normalization) {
                        $this->normalization->setCollectionStatistics($this->collectionstats);
                        $tf = $this->normalization->normalise($tf, $docLength); 
                    }

                    if ($this->lambda) {
                        $this->lambda->setCollectionStatistics($this->collectionstats);
                        $this->lambda->setEntryStatistics($entrystats);
                        $lambda = $this->lambda->getLambda();
                        if ($lambda == 1) {
                          // SPLDistribution cannot work with values of lambda that are equal to 1
                          $lambda = 0.9999999999;
                        }
                    }

                    $score[$class] += $keyFrequency * $this->probdist->score($tf, $lambda);
                }
            }
        }

        arsort($score);
        return $score;

    }

}