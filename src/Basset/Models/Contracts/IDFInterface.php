<?php

namespace Basset\Models\Contracts;


interface IDFInterface
{

	public function score($tf, $docLength, $docUniqueLength);

}
