<?php

namespace Basset\Models\DFRAfterEffect;


class B extends AfterEffect implements AfterEffectInterface
{

	public function __construct()
    {
    	parent::__construct();
    }

    public function gain($tf) {
    	return ($this->getTermFrequency() + 1) / ($this->getDocumentFrequency() * ($tf + 1));
    }

}