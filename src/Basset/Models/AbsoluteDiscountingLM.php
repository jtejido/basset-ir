<?php

namespace Basset\Models;

use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Models\Contracts\LanguageModelInterface;
use Basset\Models\Contracts\KLDivergenceLMInterface;

/**
 * AbsoluteDiscountingLM is a class for ranking documents against a query by lowering down the probability of seen words by
 * subtracting a constant from their counts.
 *
 * The effect of this is that the events with the lowest counts are discounted relatively more than those with higher counts.
 * From Chengxiang Zhai and John Lafferty. 2001. A study of smoothing methods for language models applied
 * to Ad Hoc information retrieval. In Proceedings of the 24th annual international ACM SIGIR conference on 
 * Research and development in information retrieval (SIGIR '01).
 * http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.94.8019&rep=rep1&type=pdf
 * The optimal value for 𝛿 tends to be around 0.7
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class AbsoluteDiscountingLM extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface, KLDivergenceLMInterface
{

    const DELTA = 0.7;

    protected $delta;

    public function __construct($delta = self::DELTA)
    {
        parent::__construct();
        $this->delta = $delta;
    }

    private function getConstant() {
        return $this->delta;
    }

    private function getDocumentConstant($docLength, $docUniqueLength) {
        return ($this->getConstant() * $docUniqueLength) / $docLength;
    }
 
    /**
     * Smoothed p(w|d) is max(c(w|d) - 𝛿, 0) / ∑c(w|d) + (𝛿|d'| / |d|)p(w|C)
     * Document dependent constant is 𝛿|d'| / |d| where |d'| is number of unique terms in a document
     *
     * The term weight in a form of KL divergence is given by p(w|Q)log(p(w|d)/αp(w|C)) + log α where:
     * p(w|d) = the document model.
     * p(w|C) = the collection model.
     * p(w|Q) = the query model.
     * α = document dependent constant
     *
     * Thus it becomes log(1 + (c(w|d) - 𝛿 / 𝛿|d'|p(w|C)) + log(𝛿|d'| / |d|).
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {

        $constant = $this->getConstant();
        $document_constant = $this->getDocumentConstant($docLength, $docUniqueLength);
        $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();
        
        // log(1 + ((max($tf - $this->delta, 0) / $docLength) + ((($this->delta * $docUniqueLength) / $docLength) * $mle_c)))
        return log(1 + ( ($tf-$constant) / ($constant * $docUniqueLength * $mle_c) ) );

    }

    
}