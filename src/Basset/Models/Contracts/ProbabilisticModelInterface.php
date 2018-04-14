<?php

namespace Basset\Models\Contracts;


interface ProbabilisticModelInterface
{

	public function score($tf, $docLength, $docUniqueLength);

}
