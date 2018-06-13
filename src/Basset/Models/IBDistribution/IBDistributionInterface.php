<?php


namespace Basset\Models\IBDistribution;


interface IBDistributionInterface
{

    public function score(int $tf, float $lambda): float;

}
