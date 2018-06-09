<?php

namespace Basset\Metric;


interface SimilarityInterface extends MetricInterface
{
    public function similarity(array $a, array $b): float;
}
