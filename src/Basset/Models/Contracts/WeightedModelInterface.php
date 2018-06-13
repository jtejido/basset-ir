<?php


namespace Basset\Models\Contracts;


interface WeightedModelInterface
{

    public function score(int $tf, int $docLength, int $docUniqueLength): float;

}
