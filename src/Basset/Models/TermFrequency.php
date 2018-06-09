<?php

namespace Basset\Models;

use Basset\Models\Contracts\TFInterface;
use Basset\Models\Contracts\WeightedModelInterface;

/**
 * term frequency ratio (or AKA document likelihood in LM)
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TermFrequency extends WeightedModel implements WeightedModelInterface, TFInterface
{

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {     
        return $tf / $docLength;
    }


}