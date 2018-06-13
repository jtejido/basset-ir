<?php


namespace Basset\Models;

use Basset\Models\Contracts\{
        ProbabilisticModelInterface,
        WeightedModelInterface,
        KLDivergenceLMInterface
    };
use Basset\{
        Metric\VectorSimilarity,
        Models\TermFrequency
    };

/**
 * JelinekMercerLM is a class for ranking documents against a query based on Linear interpolation of the maximum 
 * likelihood model.
 *
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 * @see http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.8019&rep=rep1&type=pdf
 * The value for λ is generally very small (0.1) for title queries and around 0.7 for verbose. Making it 1 makes term weight tends toward 
 * zero.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class JelinekMercerLM extends WeightedModel implements WeightedModelInterface, KLDivergenceLMInterface, ProbabilisticModelInterface
{

    const LAMBDA = 0.7;

    protected $lambda;

    public function __construct($lambda = self::LAMBDA)
    {
        parent::__construct();
        $this->lambda = $lambda;
        $this->queryModel = new TermFrequency;
        $this->metric = new VectorSimilarity;

    }

    private function getConstant(): float
    {
        return $this->lambda;
    }

    public function getDocumentConstant(int $docLength, int $docUniqueLength): float
    {
        return $this->getConstant();
    }
 
    /**
     * Smoothed p(w|d) is (1 - λ)p(w|d) + λp(w|C).
     * Document dependent constant is λ
     *
     * The term weight in a form of KL divergence is given by p(w|Q)log(p(w|d)/αp(w|C)) + log α where:
     * p(w|d) = the document model.
     * p(w|C) = the collection model.
     * p(w|Q) = the query model.
     * α = document dependent constant
     *
     * Thus it becomes log(1 + ((1 - λ)p(w|d) / λp(w|C))) + log(λ).
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
        // smoothed probability of words seen in the document
        $mle_d = $tf / $docLength;

        // log(1 + ( (1 - $constant) * $mle_d + ($constant * $mle_c)) );
        return log(1 + ( ((1 - $constant) * $mle_d) / ($constant * $mle_c)) );

    }

    
}