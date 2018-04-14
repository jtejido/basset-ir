<?php

namespace Basset\Models\IBDistribution;


interface IBDistributionInterface
{

    public function score($tf, $lambda);

}
