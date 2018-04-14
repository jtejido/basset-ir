<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * http://www.icsd.aegean.gr/lecturers/stamatatos/papers/survey.pdf
 */
class StamatatosDistance extends Similarity implements DistanceInterface
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
        $dist = 0;
        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])){
                $dist += pow(2 * ($this->getScore($q, $key)-$this->getScore($doc, $key)) / ($this->getScore($q, $key)+$this->getScore($doc, $key)), 2);
            }
        }

        return $dist;

    }


}
