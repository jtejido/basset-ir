<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        TFInterface,
        WeightedModelInterface
    };


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
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {     
        return $tf / $docLength;
    }


}