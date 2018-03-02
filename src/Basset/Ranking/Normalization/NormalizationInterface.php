<?php

namespace Basset\Ranking\Normalization;


interface NormalizationInterface
{

    public function normalise($tf, $docLength);

}
