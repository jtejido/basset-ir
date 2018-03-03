<?php

namespace Basset\Ranking;



/**
 * HiemstraLM is a class for ranking documents against a query based on Hiemstra's PHD thesis for language 
 * model.
 * https://pdfs.semanticscholar.org/67ba/b01706d3aada95e383f1296e5f019b869ae6.pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class HiemstraLM extends SimilarityBase implements ScoringInterface
{

    const C = 0.15;

    protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c    = $c;
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
            $score += $keyFrequency * log(1 + ( ($this->c * $tf * $this->getNumberOfTokens()) / ((1-$this->c) * $this->getTermFrequency() * $docLength)));
        }

        return $score;

    }


}