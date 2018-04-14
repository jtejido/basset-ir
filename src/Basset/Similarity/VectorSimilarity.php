<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;
use Basset\Models\Contracts\KLDivergenceLMInterface;

class VectorSimilarity extends Similarity implements SimilarityInterface
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
        $dotProduct = 0;

        $A = $q->getTokens();

        $B = $doc->getTokens();

        $uniqueKeys = $this->getAllUniqueKeys($A, $B);

        foreach ($uniqueKeys as $key) {
            if (!empty($A[$key]) && !empty($B[$key])) {
                $dotProduct += ($this->getScore($q, $key) * $this->getScore($doc, $key));
                if ($doc->getModel() instanceof KLDivergenceLMInterface) {
                        $dotProduct += $this->getDocumentConstant($doc);
                    }
            }
        }

        return $dotProduct;
    }

}
