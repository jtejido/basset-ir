<?php


namespace Basset\Models\Contracts;


interface LanguageModelInterface extends ProbabilisticModelInterface
{

	public function score(int $tf, int $docLength, int $docUniqueLength): float;

}
