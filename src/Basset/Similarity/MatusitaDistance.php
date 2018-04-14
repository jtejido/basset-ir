<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;


/**
 * K. Matusita, Decision rules, based on the distance, for problems of fit, two
 * samples, and estimation, Ann. Math. Statist. 26 (1955) 631–640 
 */
class MatusitaDistance extends Similarity implements DistanceInterface
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
                $sum += pow(sqrt($this->getScore($q, $key))-sqrt($this->getScore($doc, $key)),2);
            }
        }

        return sqrt($sum);

    }


}
