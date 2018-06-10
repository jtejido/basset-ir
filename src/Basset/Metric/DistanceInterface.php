<?php

declare(strict_types=1);

namespace Basset\Metric;


interface DistanceInterface extends MetricInterface
{
    public function dist(array $a, array $b): float;
}
