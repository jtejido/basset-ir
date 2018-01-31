<?php

namespace Basset\Ranking\BasicModel;


interface BasicModelInterface
{

    public function score($tfn, $docLength, $documentFrequency, $termFrequency, $collectionLength, $collectionCount);

}
