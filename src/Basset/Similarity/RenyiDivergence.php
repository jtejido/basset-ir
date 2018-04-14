<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

class RenyiDivergence extends Similarity implements SimilarityInterface
{
    
    CONST CONSTANT = 2;

    protected $const;

    /**
     * https://en.wikipedia.org/wiki/R%C3%A9nyi_entropy
     */

    public function __construct($constant = self::CONSTANT)
    {
      parent::__construct();
      $this->const = $constant;
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

        if($this->const == 1){
            //const = 1 is a special case where it should equal KL divergence
            return $this->klDivergence($this->getTokens($q, true), $this->getTokens($doc, true));
        }

        $uniqueKeys = $this->getAllUniqueKeys($A, $B);
        $divergence = 0;
        foreach ($uniqueKeys as $key) {

            if (!empty($A[$key]) && !empty($B[$key])){
                $num = pow($this->getScore($doc, $key), $this->const);
                $denom = pow($this->getScore($q, $key), $this->const - 1);
                $divergence += ($denom > 0) ? ($num/$denom) : 0;
            }
        }

        return $divergence > 0 ? (1/($this->const - 1)) * log($divergence) : 0;

    }


    private function klDivergence(array $A, array $B)
    {

        $uniqueKeys = $this->getAllUniqueKeys($A, $B);
        $klDiv = 0;
        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){             
                $klDiv += ($A[$key] > 0) ? ($B[$key] * log( $B[$key] / $A[$key] )) : 0;
            }
        }
        return $klDiv;
    }

}
