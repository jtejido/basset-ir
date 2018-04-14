<?php

namespace Basset\Models\DFIModels;


interface DFIInterface
{

    public function score($tf, $docLength, $docUniqueLength);

}
