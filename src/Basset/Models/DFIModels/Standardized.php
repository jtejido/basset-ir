<?php

namespace Basset\Models\DFIModels;


class Standardized extends DFIModel implements DFIInterface
{


    public function score($tf, $docLength, $docUniqueLength){
    	$expected = $this->getExpected($docLength);
        return log((($tf - $expected) / sqrt($expected)), 2);

    }

}