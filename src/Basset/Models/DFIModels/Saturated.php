<?php

declare(strict_types=1);

namespace Basset\Models\DFIModels;


class Saturated extends DFIModel implements DFIInterface
{


    public function score(int $tf, int $docLength, int $docUniqueLength): float
    {
    	$expected = $this->getExpected($docLength);
        return ($tf - $expected)/$expected;

    }

}