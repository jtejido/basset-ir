<?php

namespace Basset\Models\DFIModels;


class ChiSquared extends DFIModel implements DFIInterface
{


    public function score($tf, $docLength, $docUniqueLength){
    	$expected = $this->getExpected($docLength);
        return pow(($tf - $expected), 2)/$expected;

    }

}