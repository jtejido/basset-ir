<?php

namespace Basset\Ranking\AfterEffect;


interface AfterEffectInterface
{

    public function gain($tfn, $documentFrequency, $termFrequency);

}
