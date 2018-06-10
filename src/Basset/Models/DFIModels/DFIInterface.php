<?php

declare(strict_types=1);

namespace Basset\Models\DFIModels;


interface DFIInterface
{

    public function score(int $tf, int $docLength, int $docUniqueLength): float;

}
