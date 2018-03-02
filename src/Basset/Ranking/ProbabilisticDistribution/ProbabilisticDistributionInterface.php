<?php

namespace Basset\Ranking\ProbabilisticDistribution;


interface ProbabilisticDistributionInterface
{

    public function score($tf, $lambda);

}
