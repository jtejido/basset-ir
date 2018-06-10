<?php

declare(strict_types=1);

namespace Basset\Models\Contracts;


interface TFInterface
{

	public function score(int $tf, int $docLength, int $docUniqueLength): float;

}
