<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * http://www.itl.nist.gov/div898/handbook/eda/section3/eda35f.htm
 * The formula appeas assymetric so we'll just change it to be symmetric to both sets
 */
class ChiSquareDistance extends Similarity implements DistanceInterface
{

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @param  QueryDocument $q
     * @param  Document $doc
     * @return float
     */
    public function dist(DocumentInterface $q, DocumentInterface $doc)
    {

        $A = $this->getTokens($q);
        $B = $this->getTokens($doc);

        $sum = 0;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $num = ($this->getScore($q, $key)-$this->getScore($doc, $key)) * ($this->getScore($q, $key)-$this->getScore($doc, $key));
                $denom = ($this->getScore($q, $key)+$this->getScore($doc, $key));
                $sum += ($denom > 0) ? $num/$denom : 0  ;
            }
        }

        return $sum;

    }


}
