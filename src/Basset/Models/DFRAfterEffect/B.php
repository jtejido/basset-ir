<?php

declare(strict_types=1);

namespace Basset\Models\DFRAfterEffect;


class B extends AfterEffect implements AfterEffectInterface
{

	public function __construct()
    {
    	parent::__construct();
    }

    public function gain(int $tf): float
    {
    	return ($this->getTermFrequency() + 1) / ($this->getDocumentFrequency() * ($tf + 1));
    }

}