<?php

declare(strict_types=1);

namespace Basset\Models\DFRAfterEffect;


class L extends AfterEffect implements AfterEffectInterface
{


	public function __construct()
    {
    	parent::__construct();
    }
    
    public function gain(int $tf): float
    {
    	return 1/(1+$tf);
    }

}