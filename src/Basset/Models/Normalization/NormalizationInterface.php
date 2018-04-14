<?php

namespace Basset\Models\Normalization;


interface NormalizationInterface
{

    public function normalise($tf, $docLength);

}
