<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * https://en.wikipedia.org/wiki/Hellinger_distance
 */
class HellingerDistance extends Similarity implements DistanceInterface
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
        $A = $this->getTokens($q, true);
        $B = $this->getTokens($doc, true);
        $meanV1 = $this->math->mean(array_count_values(array_keys($A)));
        $meanV2 = $this->math->mean(array_count_values(array_keys($B)));

        $n = count($A);
        $sum = 0;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $sum += pow(sqrt($A[$key]/$meanV1)-sqrt($B[$key]/$meanV2),2);
            }
        }


        return sqrt($sum) * (1/sqrt(2));

    }


}
