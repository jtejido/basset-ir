<?php

namespace Basset\Similarity;

interface DistanceInterface
{
    public function dist(array $A, array $B);
}
