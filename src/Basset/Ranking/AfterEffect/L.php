<?php

namespace Basset\Ranking\AfterEffect;


class L extends AfterEffect implements AfterEffectInterface
{

    public function gain($tf) {
    	return 1/(1+$tf);
    }

}