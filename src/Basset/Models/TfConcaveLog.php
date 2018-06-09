<?php

namespace Basset\Models;

use Basset\Models\Contracts\TFInterface;
use Basset\Models\Contracts\WeightedModelInterface;

/**
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class TfConcaveLog extends WeightedModel implements WeightedModelInterface, TFInterface
{


    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength)
    {
        return 1 + log(1 + log($tf));
    }


}