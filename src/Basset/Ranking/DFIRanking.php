<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\PostingStatistics;
use Basset\Statistics\EntryStatistics;
use Basset\Ranking\Divergence\DFIInterface;
use Basset\Ranking\IDF\IdfInterface;


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


class DFIRanking extends AbstractRanking
{

    protected $dfi;

    protected $idf;

    protected $collectionstats;

    public function __construct(DFIInterface $dfi, IdfInterface $idf, CollectionSet $set)
    {
        parent::__construct($set);
        $this->dfi    = $dfi;
        $this->idf    = $idf;
        $this->collectionstats = new CollectionStatistics($this->set);

        if ($this->dfi == null || $this->idf == null) {
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
                
                $expected = ($entrystats->getTermFrequency() * $docLength) / $this->collectionstats->getNumberOfTokens();
                
                if($tf > $expected) {

                    if ($this->dfi) {
                        $dfi = $this->dfi->getDFI($tf, $expected);
                    }

                    if ($this->idf) {
                        $this->idf->setCollectionStatistics($this->collectionstats);
                        $this->idf->setEntryStatistics($entrystats);
                        $idf = $this->idf->getIdf();
                    }

                    $score[$class] += ($keyFrequency * $dfi * $idf);
                }
            }
        }

        arsort($score);
        return $score;

    }

}