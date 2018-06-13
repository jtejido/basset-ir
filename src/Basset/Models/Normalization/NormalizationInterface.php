<?php


namespace Basset\Models\Normalization;


interface NormalizationInterface
{

    public function normalise(int $tf, int $docLength): float;

}
