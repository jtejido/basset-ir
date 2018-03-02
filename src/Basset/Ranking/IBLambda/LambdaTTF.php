<?php

namespace Basset\Ranking\IBLambda;



class LambdaTTF extends Lambda implements IBLambdaInterface
{


    public function getLambda(){

        return ($this->getTermFrequency()+1) / ($this->getNumberOfDocuments()+1);

    }

}