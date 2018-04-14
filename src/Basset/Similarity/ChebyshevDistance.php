<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * https://en.wikipedia.org/wiki/Chebyshev_distance
 * AKA Maximum Metric
 */
class ChebyshevDistance extends Similarity implements DistanceInterface
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

        $max = 0;
        $aux = 0;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $aux += abs($this->getScore($q, $key)-$this->getScore($doc, $key));
                if ($max < $aux) {
                    $max = $aux;
                }
            }
        }


        return $max;

    }


}
