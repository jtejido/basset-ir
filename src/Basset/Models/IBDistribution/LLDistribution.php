<?php

declare(strict_types=1);

namespace Basset\Models\IBDistribution;


class LLDistribution implements IBDistributionInterface
{


    public function score(int $tf, float $lambda): float
    {

        return -log($lambda / ($tf + $lambda));

    }

}