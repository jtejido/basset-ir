<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\PostingStatistics;
use Basset\Statistics\EntryStatistics;
use Basset\Ranking\BasicModel\BasicModelInterface;
use Basset\Ranking\AfterEffect\AfterEffectInterface;
use Basset\Ranking\Normalization\NormalizationInterface;


/**
 * DFR is a framework for ranking documents against a query based on Harter's 2-Poisson index-model.
 * S.P. Harter. A probabilistic approach to automatic keyword indexing. PhD thesis, Graduate Library, The University of
 * Chicago, Thesis No. T25146, 1974
 * This class provides an alternative way of specifying an arbitrary DFR weighting model, by mixing the used components.
 *
 * The implementation is strictly based on G. Amati, C. Rijsbergen paper:
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.97.8274&rep=rep1&type=pdf
 *
 * DFR models are obtained by instantiating the three components of the framework: 
 * selecting a basic randomness model, applying the first normalisation and normalising the term frequencies.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DFRRanking extends AbstractRanking
{

    protected $basicmodel;

    protected $aftereffect;

    protected $normalization;

    protected $collectionstats;

    public function __construct(BasicModelInterface $basicmodel, AfterEffectInterface $aftereffect, NormalizationInterface $normalization, CollectionSet $set)
    {
        parent::__construct($set);
        $this->basicmodel    = $basicmodel;
        $this->aftereffect    = $aftereffect;
        $this->normalization    = $normalization;
        $this->collectionstats = new CollectionStatistics($this->set);

        if ($this->basicmodel == null || $this->aftereffect == null || $this->normalization == null) {
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

                    $gain = 1;

                    if ($this->aftereffect) {
                        $this->aftereffect->setEntryStatistics($entrystats);
                        $gain = $this->aftereffect->gain($tf);
                    }

                    $this->basicmodel->setEntryStatistics($entrystats);
                    $this->basicmodel->setCollectionStatistics($this->collectionstats);
                    $score[$class] += $keyFrequency * $gain * $this->basicmodel->score($tf);
                }
            }
        }

        arsort($score);
        return $score;

    }

}