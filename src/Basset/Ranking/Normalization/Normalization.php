<?php

namespace Basset\Ranking\Normalization;

use Basset\Ranking\WeightedModel;

abstract class Normalization extends WeightedModel
{

    abstract protected function normalise($tf, $docLength);

}