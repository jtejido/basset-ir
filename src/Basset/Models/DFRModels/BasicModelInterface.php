<?php


namespace Basset\Models\DFRModels;


interface BasicModelInterface
{

    public function score(int $tf, int $docLength, int $docUniqueLength): float;

}
