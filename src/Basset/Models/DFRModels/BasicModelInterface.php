<?php

namespace Basset\Models\DFRModels;


interface BasicModelInterface
{

    public function score($tf, $docLength, $docUniqueLength);

}
