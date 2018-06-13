<?php


namespace Basset\Models\DFIModels;


interface DFIInterface
{

    public function score(int $tf, int $docLength, int $docUniqueLength): float;

}
