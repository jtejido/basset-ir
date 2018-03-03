<?php

namespace Basset\Ranking\IBLambda;

use Basset\Ranking\WeightedModel;

abstract class Lambda extends WeightedModel
{

    abstract protected function getLambda();

}