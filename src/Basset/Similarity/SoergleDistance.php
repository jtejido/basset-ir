<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * http://www.orgchm.bas.bg/~vmonev/SimSearch.pdf
 */
class SoergleDistance extends Similarity implements DistanceInterface
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
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $num += abs($this->getScore($doc, $key) - $this->getScore($q, $key));
                $denom += max($this->getScore($doc, $key), $this->getScore($q, $key));
            }
        }

        return $denom != 0 ? $num/$denom : 0;

    }


}
