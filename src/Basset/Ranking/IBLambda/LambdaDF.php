<?php

namespace Basset\Ranking\IBLambda;



class LambdaDF extends Lambda implements IBLambdaInterface
{
	
	public function __construct()
    {
    	parent::__construct();
    }

    public function getLambda(){

        return ($this->getDocumentFrequency()+1) / ($this->getNumberOfDocuments()+1);

    }

}