<?php


namespace Basset\Models\Contracts;


interface KLDivergenceLMInterface extends LanguageModelInterface
{

	public function getDocumentConstant(int $docLength, int $docUniqueLength): float;

	public function score(int $tf, int $docLength, int $docUniqueLength): float;

}
