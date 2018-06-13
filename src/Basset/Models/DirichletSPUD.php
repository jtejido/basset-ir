<?php


namespace Basset\Models;

use Basset\Models\Contracts\{
        ProbabilisticModelInterface,
        WeightedModelInterface
    };

/**
 * DirichletSPUD is a class for ranking documents that captures the tendency of a term to repeat itself within 
 * a document (i.e. word burstiness).
 *
 * From Ronan Cummins et al. 2015. A Polya Urn Document Language Model for Improved Information Retrieval.
 * @see https://arxiv.org/pdf/1502.00804.pdf
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

        throw new \Exception("DirichletSPUD won't be used for the time being.");
        
        parent::__construct();
        $this->omega = $omega;
        $this->iteration = $iteration;
        $this->m = $m;
    }
 
    /**
     * Estimation of the Background DCM (EDCM) via Multivariate Polya Distribution
     *
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

        $totalterms = $this->getUniqueTotalByTermPresence();
        
        $belief = $this->getBelief();

        $mle_document = $tf/$docLength;

        $topic_model = $docUniqueLength * $mle_document;

        $background_model = $this->getDocumentFrequency()/$totalterms;

        return log( 1 + ( ((1-$this->omega) * $topic_model + $this->omega * $belief * $background_model) / ((1-$this->omega) * $docUniqueLength + $this->omega * $belief) ) );


    }

    
}