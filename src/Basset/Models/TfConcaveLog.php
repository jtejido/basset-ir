<?php

declare(strict_types=1);

namespace Basset\Models;

use Basset\Models\Contracts\{
        TFInterface,
        WeightedModelInterface
    };


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
    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {
        return 1 + log(1 + log($tf));
    }


}