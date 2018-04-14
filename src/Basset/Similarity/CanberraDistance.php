<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * https://en.wikipedia.org/wiki/Canberra_distance
 */
class CanberraDistance extends Similarity implements DistanceInterface
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
                $num = abs($this->getScore($q, $key)-$this->getScore($doc, $key));
                $denom = $this->getScore($q, $key)+$this->getScore($doc, $key);
                $sum += ($denom > 0) ? $num/$denom : 0  ;
            }
        }

        return $sum;

    }


}
