<?php


namespace Basset\Models\IBLambda;



class LambdaTTF extends Lambda implements IBLambdaInterface
{

	public function __construct()
    {
    	parent::__construct();
    }
    
    public function getLambda(): float
    {

        return ($this->getTermFrequency()+1) / ($this->getNumberOfDocuments()+1);

    }

}