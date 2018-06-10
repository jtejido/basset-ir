<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        TFInterface,
        WeightedModelInterface
    };



/**
 * simple term count implementation
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TermCount extends WeightedModel implements WeightedModelInterface, TFInterface
{

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {  
        return $tf;
    }


}