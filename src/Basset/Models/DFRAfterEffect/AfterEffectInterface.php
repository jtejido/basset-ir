<?php

declare(strict_types=1);

namespace Basset\Models\DFRAfterEffect;


interface AfterEffectInterface
{

    public function gain(int $tf): float;

}
