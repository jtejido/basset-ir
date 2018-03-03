<?php

namespace Basset\Ranking\IDF;

use Basset\Ranking\WeightedModel;
use Basset\Statistics\EntryStatistics;
use Basset\Statistics\CollectionStatistics;
use Basset\Math\Math;

abstract class BaseIdf extends WeightedModel
{

    abstract protected function getIdf();

}