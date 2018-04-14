<?php

namespace Basset\Models\Contracts;


interface LanguageModelInterface extends ProbabilisticModelInterface
{

	public function score($tf, $docLength, $docUniqueLength);

}
