<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * https://arxiv.org/pdf/0802.4376.pdf
 */
class LorentzianDistance extends Similarity implements DistanceInterface
{

    public function __construct()
    {
      parent::__construct();
    }
    
    /**
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function dist(DocumentInterface $q, DocumentInterface $doc)
    {
        $A = $this->getTokens($q);
        $B = $this->getTokens($doc);
        $dist = 0;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $dist += log(abs($this->getScore($q, $key)-$this->getScore($doc, $key)) + 1);
            }
        }


        return $dist;

    }


}
