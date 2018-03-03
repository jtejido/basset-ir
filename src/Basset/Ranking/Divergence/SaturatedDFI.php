<?php

namespace Basset\Ranking\Divergence;


class SaturatedDFI implements DFIInterface
{


    public function getDFI($tf, $expected){

        return ($tf - $expected)/$expected;

    }

}