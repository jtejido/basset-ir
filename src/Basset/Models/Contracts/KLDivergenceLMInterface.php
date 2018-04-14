<?php

namespace Basset\Models\Contracts;


interface KLDivergenceLMInterface extends LanguageModelInterface
{

	public function getDocumentConstant($docLength, $docUniqueLength);

	public function score($tf, $docLength, $docUniqueLength);

}
