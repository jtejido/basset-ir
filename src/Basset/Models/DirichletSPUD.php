<?php

namespace Basset\Models;

use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;

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


class DirichletSPUD extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    const OMEGA = 0.8;

    const ITERATION = 10;

    const M = 250;

    protected $omega;

    protected $iteration;

    protected $m;


    public function __construct($omega = self::OMEGA, $iteration = self::ITERATION, $m = self::M)
    {
        parent::__construct();
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
                $denom += $this->math->digamma( $mc + array_sum($array[$j]) );
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
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {

        $totalterms = $this->getUniqueTotalByTermPresence();
        
        $belief = $this->getBelief($this->getDocsByTermPresence(), $totalterms);

        $mle_document = $tf/$docLength;

        $topic_model = $docUniqueLength * $mle_document;

        $background_model = $this->getDocumentFrequency()/$totalterms;

        return log( 1 + ( ((1-$this->omega) * $topic_model + $this->omega * $belief * $background_model) / ((1-$this->omega) * $docUniqueLength + $this->omega * $belief) ) );


    }

    
}