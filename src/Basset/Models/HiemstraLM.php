<?php

namespace Basset\Models;

use Basset\Models\Contracts\ProbabilisticModelInterface;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Metric\VectorSimilarity;
use Basset\Models\TermCount;


/**
 * HiemstraLM is a class for ranking documents against a query based on Hiemstra's PHD thesis for language 
 * model.
 * https://pdfs.semanticscholar.org/67ba/b01706d3aada95e383f1296e5f019b869ae6.pdf
 *
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class HiemstraLM extends WeightedModel implements WeightedModelInterface, ProbabilisticModelInterface
{

    const C = 0.15;

    protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c    = $c;
        $this->queryModel = new TermCount;
        $this->metric = new VectorSimilarity;
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {

        return log(1 + ( ($this->c * $tf * $this->getTotalByTermPresence()) / ((1-$this->c) * $this->getDocumentFrequency() * $docLength)));

    }


}