<?php

namespace Basset\Models\Contracts;


interface TFInterface
{

	public function score($tf, $docLength, $docUniqueLength);

}
