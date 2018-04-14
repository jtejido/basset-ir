<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;


/**
 * http://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf
 */
class RuzickaSimilarity extends Similarity implements SimilarityInterface
{
    /**
     * @param  QueryDocument $q
     * @param  Document $doc
     * @return float
     */
    public function similarity(DocumentInterface $q, DocumentInterface $doc)
    {
        $A = $this->getTokens($q);
        $B = $this->getTokens($doc);
        $num = 0;
        $denom = 0;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $num += min($this->getScore($q, $key), $this->getScore($doc, $key));
                $denom += max($this->getScore($q, $key), $this->getScore($doc, $key));
            }
        }


        return $denom != 0 ? $num/$denom : 0;
    }

}