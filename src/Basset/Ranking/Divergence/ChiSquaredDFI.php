<?php

namespace Basset\Ranking\Divergence;


class ChiSquaredDFI implements DFIInterface
{


    public function getDFI($tf, $expected){

        return pow(($tf - $expected), 2)/$expected;

    }

}