<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * http://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf
 */
class KulczynskiSimilarity extends Similarity implements SimilarityInterface
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
    public function similarity(DocumentInterface $q, DocumentInterface $doc)
    {
        $A = $this->getTokens($q);
        $B = $this->getTokens($doc);
        $num = 0;
        $denom = 0;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $num += abs($this->getScore($doc, $key) - $this->getScore($q, $key));
                $denom += min($this->getScore($doc, $key), $this->getScore($q, $key));
            }
        }


        return ($denom > 0) ? $num/$denom : 0;
    }

}