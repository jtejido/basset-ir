<?php

declare(strict_types=1);

namespace Basset\Models\Normalization;


interface NormalizationInterface
{

    public function normalise(int $tf, int $docLength): float;

}
