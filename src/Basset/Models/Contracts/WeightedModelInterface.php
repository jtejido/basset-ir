<?php

namespace Basset\Models\Contracts;


interface WeightedModelInterface
{

    public function score($tf, $docLength, $docUniqueLength);

}
