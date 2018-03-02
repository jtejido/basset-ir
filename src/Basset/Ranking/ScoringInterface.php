<?php

namespace Basset\Ranking;


interface ScoringInterface
{

    public function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength);

}
