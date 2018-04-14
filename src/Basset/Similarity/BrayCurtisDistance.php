<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * Bray, J. R. and J. T. Curtis. 1957. An ordination of upland forest communities of southern Wisconsin. 
 * Ecological Monographs 27:325-349.
 * http://84.89.132.1/~michael/stanford/maeb5.pdf
 */

class BrayCurtisDistance extends Similarity implements DistanceInterface
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

        $num = 0;
        $denom = 0;
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));

        $uniqueKeys = array_unique(array_merge($keysA, $keysB));
        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $num +=  abs($this->getScore($q, $key) - $this->getScore($doc, $key));
                $denom += ($this->getScore($q, $key)+$this->getScore($doc, $key));
            }
        }

        return $denom != 0 ? $num/$denom : 0;

    }


}
