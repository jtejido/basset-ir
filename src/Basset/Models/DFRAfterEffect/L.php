<?php

namespace Basset\Models\DFRAfterEffect;


class L extends AfterEffect implements AfterEffectInterface
{


	public function __construct()
    {
    	parent::__construct();
    }
    
    public function gain($tf) {
    	return 1/(1+$tf);
    }

}