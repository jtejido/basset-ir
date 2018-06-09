<?php

namespace Basset\Metric;


interface DistanceInterface extends MetricInterface
{
    public function dist(array $a, array $b): float;
}
