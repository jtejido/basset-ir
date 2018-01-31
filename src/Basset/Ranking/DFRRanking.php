<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\FeatureExtraction\TfFeatureExtraction;
use Basset\Statistics\Statistics;
use Basset\Ranking\BasicModel\BasicModelInterface;
use Basset\Ranking\AfterEffect\AfterEffectInterface;
use Basset\Ranking\Normalization\NormalizationInterface;


/**
 * DFRWeightingModel is a framework for ranking documents against a query based on Harter's 2-Poisson index-model.
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

    protected $query;

    protected $score;

    protected $basicmodel;

    protected $aftereffect;

    protected $normalization;

    protected $stats;

    public function __construct(BasicModelInterface $basicmodel, AfterEffectInterface $aftereffect, NormalizationInterface $normalization, CollectionSet $set)
    {
        parent::__construct($set);
        $this->basicmodel    = $basicmodel;
        $this->aftereffect    = $aftereffect;
        $this->normalization    = $normalization;
        $this->stats = new Statistics($this->set);

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

        //∑(Document, Query)
        $collectionLength = $this->stats->numberofCollectionTokens();
        $collectionCount = $this->stats->numberofDocuments();

        foreach($this->set as $class => $doc) {
            $this->vector = $this->getVector($doc);
            $score[$class] = isset($score[$class]) ? $score[$class] : 0;
            $docLength = $this->getDocLength($doc);
            foreach ($q->getDocument() as $term){
                $documentFrequency = $this->stats->documentFrequency($term);
                $termFrequency = $this->stats->termFrequency($term);
                $keyFrequency = $this->keyFrequency($q->getDocument(), $term);
                $tf = $this->getTf($doc, $term);
                if($tf != 0) {
                    $tfn = $tf;

                    if($this->normalization) {
                        $tfn = $this->normalization->normalise($tf, $docLength, $termFrequency, $collectionLength); 
                    }

                    $gain = 1;

                    if ($this->aftereffect) {
                        $gain = $this->aftereffect->gain($tfn, $documentFrequency, $termFrequency);
                    }
                    // ∑qtf x gain x Inf1(tf)
                    $score[$class] += $keyFrequency * $gain * $this->basicmodel->score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount);
                }
            }
        }

        arsort($score);
        return $score;

    }

    private function getTf(DocumentInterface $d, $term) {
        return isset($this->vector[$term]) ? $this->vector[$term] : 0; 
    }

    private function getDocLength(DocumentInterface $d) {
        return array_sum($this->vector);
    }

    private function getVector(DocumentInterface $d) {
         $tffe = new TfFeatureExtraction();
         return $tffe->getFeature($d);
    }

}