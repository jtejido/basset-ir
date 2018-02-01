<?php

namespace Basset\Ranking;

use Basset\Math\Math;
use Basset\Ranking\ScoringInterface;


/**
 * Information-based models are family of classes for ranking documents against a query based on the fact that the 
 * difference in the behaviors of a word at the document and collection levels brings information on the significance 
 * of the word for the document (2-Poisson, see DFR classes) combined with Church and Gale's phenomenon of term burstiness.
 * 'Once they appear in a document, they are much more likely to appear again'.
 * Clinchant & Gaussier
 * http://citeseerx.ist.psu.edu/viewdoc/summary?doi=10.1.1.412.7409
 * Smoothed Power-Law (SPL) distribution Class.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class SPLDistribution implements ScoringInterface
{

    const DF = 1;

    const TTF = 2;

    protected $math;

    protected $type;

    public function __construct($type = self::DF)
    {
        $this->math = new Math();
        $this->type = $type;

    }
 
    /**
     * @param  string $term
     * @return float
     */
    public function score($tf, $docLength, $documentFrequency, $keyFrequency, $termFrequency, $collectionLength, $collectionCount, $uniqueTermsCount, $keylength)
    {
        $score = 0;


        if($tf != 0){
            if($this->type == self::DF){
                $lambda = ($documentFrequency+1) / ($collectionCount+1);
            } elseif($this->type == self::TTF){
                $lambda = ($termFrequency+1) / ($collectionCount+1);
            } else {
                throw new \Exception("Type is not allowed.");
            }

            if ($lambda == 1) {
              // SPLDistribution cannot work with values of lambda that are equal to 1
              $lambda = 0.9999999999;
            }
            $exp = $tf/($tf + 1);

            $score += $keyFrequency * (-log((pow($lambda, $exp) - $lambda) / (1 - $lambda)));
        }

        return $score;
        

    }

    
}