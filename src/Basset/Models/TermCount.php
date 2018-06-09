<?php

namespace Basset\Models;

use Basset\Models\Contracts\TFInterface;
use Basset\Models\Contracts\WeightedModelInterface;


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
    public function score($tf, $docLength, $docUniqueLength)
    {  
        return $tf;
    }


}