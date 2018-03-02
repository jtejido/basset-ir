<?php

namespace Basset\Ranking;

use Basset\Math\Math;


/**
 * DirichletSPUD is a class for ranking documents that captures the tendency of a term to repeat itself within a document 
 * (i.e. word burstiness).
 *
 * From Ronan Cummins et al. 2015. A Polya Urn Document Language Model for Improved Information Retrieval.
 * https://arxiv.org/pdf/1502.00804.pdf
 * The optimal for μ appears to have a wide range (500-10000).
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DirichletSPUD extends SimilarityBase implements ScoringInterface
{

    const OMEGA = 0.8;

    const ITERATION = 10;

    const M = 250;

    protected $omega;

    protected $iteration;

    protected $m;

    protected $math;


    public function __construct($omega = self::OMEGA, $iteration = self::ITERATION, $m = self::M)
    {
        $this->math = new Math();
        $this->omega = $omega;
        $this->iteration = $iteration;
        $this->m = $m;
    }

    /**
     * Belief in the expected value of the Dirichlet thru Newton's method.
     * An initial value of mc = 250 was suitable so that the process converged within 10-20 iterations.
     *
     * @param  array $docs
     * @param  int $totalterms
     * @return float
     */
    private function getBelief($docs, $totalterms) {
        $mc = $this->m;

        $array = array_values($docs);

        for($i = 1; $i<=$this->iteration; $i++){
            $denom = 0;
            for($j = 0; $j < count($array); $j++){
                $denom += $this->math->digamma( $mc + count($array[$j]) );
            }
            $denom = $denom - count($array) * $this->math->digamma($mc);
            $mc = $totalterms / $denom;
        }

        return $mc;
    }
 
    /**
     * Estimation of the Background DCM (EDCM) via Multivariate Polya Distribution
     *
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

        if($tf > 0){
            $totalterms = $this->getTotalByTermPresence();
            $belief = $this->getBelief($this->getDocsByTermPresence(), $totalterms);
            $mle_document = $tf/$docLength;

            $score += $keyFrequency * log( 1 + ( ((1-$this->omega) * $docUniqueLength * $mle_document + $this->omega * $belief * ($this->getDocumentFrequency()/$totalterms)) / ((1-$this->omega) * $docUniqueLength + $this->omega * $belief) ) );

        }

        return $score;

    }

    
}