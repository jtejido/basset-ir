<?php

namespace Basset\Ranking;


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


class AbsoluteDiscountingLM extends SimilarityBase implements ScoringInterface
{

    const DELTA = 0.7;

    protected $delta;

    public function __construct($delta = self::DELTA)
    {
        parent::__construct();
        $this->delta = $delta;

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

        if($tf > 0){
            $mle_c = $this->getTermFrequency() / $this->getNumberOfTokens();
            $sigma = ($this->delta * $docUniqueLength) / $docLength;
            $score += $keyFrequency * log(1 + ((max($tf - $this->delta, 0) / $docLength) + ($sigma * $mle_c)));
        }

        return $score;

    }

    
}