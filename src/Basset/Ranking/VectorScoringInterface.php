<?php

namespace Basset\Ranking;


interface VectorScoringInterface
{

    public function score($query, $documents);

}
