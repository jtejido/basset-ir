<?php

declare(strict_types=1);

namespace Basset\Models\IBDistribution;


interface IBDistributionInterface
{

    public function score(int $tf, float $lambda): float;

}
