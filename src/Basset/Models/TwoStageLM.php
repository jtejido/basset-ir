<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        ProbabilisticModelInterface,
        WeightedModelInterface,
        LanguageModelInterface,
        KLDivergenceLMInterface
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermFrequency
    };


/**
 * TwoStageLM is a class for ranking documents that explicitly captures the different influences of the query and document 
 * collection on the optimal settings of retrieval parameters.
 * It involves two steps. Estimate a document language for the model, and Compute the query likelihood using the estimated 
 * language model. (DirichletLM and JelinkedMercerLM)
 *
 * From Chengxiang Zhai and John Lafferty. 2002. Two-Stage Language Models for Information Retrieval.
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.7.3316&rep=rep1&type=pdf
 * 
 * In a nutshell, this is a generalization of JelinkedMercerLM and DirichletLM.
 * The default values used here are the same constants found from the two classes.
 * Thus, making λ = 1 and μ same value as DirichletLM Class resolves the score towards DirichletLM, while as making μ larger
 * and λ same value as JelinekMercerLM Class resolves the score towards JelinekMercerLM.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TwoStageLM extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface, LanguageModelInterface, KLDivergenceLMInterface
{

    const LAMBDA = 0.7;

    const MU = 2500;

    protected $lambda;

    protected $mu;

    public function __construct($lambda = self::LAMBDA, $mu = self::MU)
    {
        parent::__construct();
        $this->mu = $mu;
        $this->lambda = $lambda;
        $this->queryModel = new TermFrequency;
        $this->metric = new VectorSimilarity;
    }

    private function getConstant(string $constant): float
    {
        if($constant === 'lambda') {
            return $this->lambda;
        } elseif($constant === 'mu') {
            return $this->mu;
        } else {
            throw new Exception('Only \'mu\' and \'lambda\' are accepted');
        }
    }

    public function getDocumentConstant(int $docLength, int $docUniqueLength): float
    {
        return ((1-$this->getConstant('lambda')) * $docLength + $this->getConstant('mu')) / ($docLength + $this->getConstant('mu'));
    }
 
    /**
     * Smoothed p(w|d) is ((1 - λ)(c(w|d) + (μp(w|C))) / (|d| + μ)) + λp(w|C));
     * Document dependent constant is (1-λ)|d| + μ / (|d| + μ)
     *
     * The term weight in a form of KL divergence is given by p(w|Q)log(p(w|d)/αp(w|C)) + log α where:
     * p(w|d) = the document model.
     * p(w|C) = the collection model.
     * p(w|Q) = the query model.
     * α = document dependent constant
     *
     * Thus it becomes log(1 + (λc(w|d) / ((1-λ)|d| + μ)p(w|C)) + log((1-λ)|d| + μ / (|d| + μ)).
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {

            $mu = $this->getConstant('mu');
            $lambda = $this->getConstant('lambda');
            $document_constant = $this->getDocumentConstant($docLength, $docUniqueLength);
            // smoothed probability of words unseen in the collection
            $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();

            // log(1 + (((1 - $this->lambda) * ($tf + ($this->mu * $mle_c)) / ($docLength + $this->mu)) + ($this->lambda * $mle_c)))
            return log(1 + (($lambda * $tf) / (((1-$lambda) * $docLength + $mu) * $mle_c )));


    }

    
}