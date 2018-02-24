<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;

abstract class AbstractRanking
{

    protected $set;

    protected $keyValues;

    public function __construct(CollectionSet $set)
    {
        $this->set = $set;
        if(count($this->set) === 0){
           throw new \InvalidArgumentException(
                 "There are no Documents added."
            ); 
        }

        
    }

    /**
     * Returns the frequency of each terms in the query.
     *
     * @param  string $term
     * @param  array $query
     * @return int
     */
    protected function keyFrequency(array $query, $term) {
        $this->keyValues = array_count_values($query);
        if(array_key_exists($term, $this->keyValues)) {
            return $this->keyValues[$term];
        } else {
            return 0;
        }
    }


    abstract protected function search(DocumentInterface $q);

}