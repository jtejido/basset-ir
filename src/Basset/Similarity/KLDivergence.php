<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

class KLDivergence extends Similarity implements SimilarityInterface
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
        $klDiv = 0;
        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $klDiv += ($this->getScore($q, $key) > 0) ? ($this->getScore($doc, $key) * log($this->getScore($doc, $key)/$this->getScore($q, $key))) : 0;
            }
        }

        return $klDiv;

    }

}
