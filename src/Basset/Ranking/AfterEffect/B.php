<?php

namespace Basset\Ranking\AfterEffect;


class B extends AfterEffect implements AfterEffectInterface
{


    public function gain($tf) {
    	return ($this->getTermFrequency() + 1) / ($this->getDocumentFrequency() * ($tf + 1));
    }

}