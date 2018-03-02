<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;

abstract class AbstractRanking
{

    protected $set;

    public function __construct(CollectionSet $set)
    {
        $this->set = $set;
        if(count($this->set) === 0){
           throw new \InvalidArgumentException(
                 "There are no Documents added."
            ); 
        }

        
    }


    abstract protected function search(DocumentInterface $q);

}