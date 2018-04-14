<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * https://en.wikipedia.org/wiki/Jensen%E2%80%93Shannon_divergence
 */
class JSDivergence extends Similarity implements SimilarityInterface
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
      $uniqueKeys = $this->getAllUniqueKeys($A, $B);
      foreach ($uniqueKeys as $key) {
        $average[$key] = 0;
        if (!empty($A[$key]) && !empty($B[$key])){
            $average[$key] += ($this->getScore($q, $key) + $this->getScore($doc, $key))/2;
        }
      }
      return ($this->klDivergence($q, $average) + $this->klDivergence($doc, $average))/2;

    }

    private function klDivergence(DocumentInterface $a, array $b)
    {

        $A = $this->getTokens($a);
        $B = $b;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);
        $klDiv = 0;
        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){             
                $klDiv += ($this->getScore($a, $key) > 0) ? ($B[$key] * log($B[$key]/$this->getScore($a, $key))) : 0;
            }
        }
        return $klDiv;
    }

}
