<?php


namespace Basset\Models\DFRAfterEffect;


interface AfterEffectInterface
{

    public function gain(int $tf): float;

}
