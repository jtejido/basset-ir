<?php

namespace Basset\Models\DFIModels;


class Saturated extends DFIModel implements DFIInterface
{


    public function score($tf, $docLength, $docUniqueLength){
    	$expected = $this->getExpected($docLength);
        return ($tf - $expected)/$expected;

    }

}