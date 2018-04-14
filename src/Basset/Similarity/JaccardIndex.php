<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;


/**
 * http://en.wikipedia.org/wiki/Jaccard_index
 */
class JaccardIndex extends Similarity implements SimilarityInterface
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
        $A = $this->getTokens($q, true);
        $B = $this->getTokens($doc, true);

        $a = array_fill_keys($A,1);
        $b = array_fill_keys($B,1);

        $intersect = count(array_intersect_key($a,$b));
        $union = count(array_fill_keys(array_merge($A,$B),1));

        return $intersect/$union;
    }

}
