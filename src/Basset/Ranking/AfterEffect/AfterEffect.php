<?php

namespace Basset\Ranking\AfterEffect;

use Basset\Ranking\WeightedModel;

abstract class AfterEffect extends WeightedModel
{
    
    abstract protected function gain($tf);

}