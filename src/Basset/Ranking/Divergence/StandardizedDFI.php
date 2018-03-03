<?php

namespace Basset\Ranking\Divergence;

use Basset\Math\Math;


class StandardizedDFI implements DFIInterface
{

	protected $math;

	public function __construct()
    {

        $this->math = new Math();

    }


    public function getDFI($tf, $expected){

        return $this->math->DFRlog(($tf - $expected) / sqrt($expected));

    }

}