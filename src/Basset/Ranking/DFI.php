<?php

namespace Basset\Ranking;

use Basset\Math\Math;


/**
 * Divergence from Independence (DFI) is a non-parametric/parameter-free DFR-counterpart class for ranking documents
 * against a query based on Chi-square statistics.
 * Kocabas, Dincer & Karaoglan
 * http://dx.doi.org/10.1007/s10791-013-9225-4
 * http://trec.nist.gov/pubs/trec18/papers/muglau.WEB.MQ.pdf
 * It is recommended NOT to remove "stopwords list". From their intro:
 *
 * -- Their observed frequencies in individual documents is expected to fluctuate around their frequencies
 * expected under independence, such words can be modeled as if they were tags. --
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DFI extends SimilarityBase implements ScoringInterface
{

    const SATURATED = 1;

    const CHI_SQUARED = 2;

    const STANDARDIZED = 3;

    protected $math;

    protected $type;

    public function __construct($type = self::CHI_SQUARED)
    {
        $this->type = $type;
        $this->math = new Math();

    }
 
    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @param  int $keyFrequency
     * @param  int $keylength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength)
    {
        $score = 0;
        $expected = ($this->getTermFrequency() * $docLength) / $this->getNumberOfTokens();

        if($tf <= $expected){
            return $score;
        }

            if($this->type == self::SATURATED) {
                $measure = ($tf - $expected)/$expected;
            } elseif($this->type == self::STANDARDIZED) {
                $measure = ($tf - $expected) / sqrt($expected);
            } elseif($this->type == self::CHI_SQUARED) {
                $measure = pow(($tf - $expected), 2)/$expected;
            }
            $score += $keyFrequency * $this->math->DFRlog($measure + 1);
            return $score;
        

    }

    
}