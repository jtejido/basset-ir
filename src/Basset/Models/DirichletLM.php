<?php


namespace Basset\Models;

use Basset\Models\Contracts\{
        WeightedModelInterface,
        LanguageModelInterface
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermFrequency
    };

/**
 * DirichletLM is a class for ranking documents against a query based on Bayesian smoothing with 
 * Dirichlet Prior for language modelling.
 *
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 * @see http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.8019&rep=rep1&type=pdf
 * The optimal for μ appears to have a wide range (500-10000).
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DirichletLM extends WeightedModel implements WeightedModelInterface, LanguageModelInterface
{

    const MU = 2500;

    protected $mu;

    public function __construct(float $mu = self::MU)
    {
        parent::__construct();
        $this->mu = $mu;
        $this->queryModel = new TermFrequency;
        $this->metric = new VectorSimilarity;

    }

    private function getConstant(): float
    {
        return $this->mu;
    }

    public function getDocumentConstant(int $docLength, int $docUniqueLength): float
    {
        return $this->getConstant()/($this->getConstant() + $docLength);
    }
 
    /**
     * Smoothed p(w|d) is c(w|d) + μp(w|C) / ∑c(w|d) + μ
     * Document dependent constant is μ / μ + ∑c(w|d)
     *
     * The term weight in a form of KL divergence is given by p(w|Q)log(p(w|d)/αp(w|C)) + log α where:
     * p(w|d) = the document model.
     * p(w|C) = the collection model.
     * p(w|Q) = the query model.
     * α = document dependent constant
     *
     * Thus it becomes log(1 + (c(w|d) / μp(w|C)) + log(μ/μ+∑c(w|d)).
     *
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {
        
        $constant = $this->getConstant();
        $document_constant = $this->getDocumentConstant($docLength, $docUniqueLength);
        // smoothed probability of words seen in the collection
        
        $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();

        // log(1 + ($tf + $constant * $mle_c) / ($docLength + $constant));
        return log(1 + ($tf / ($constant * $mle_c))) + log($document_constant);

    }

    
}