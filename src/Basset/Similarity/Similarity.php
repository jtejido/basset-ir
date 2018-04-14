<?php

namespace Basset\Similarity;

use Basset\Math\Math;
use Basset\Documents\DocumentInterface;
use Basset\Statistics\PostingStatistics;
use Basset\Models\ParsimoniousLM;


class Similarity
{

    protected $querymodel;

    protected $documentmodel;

    protected $math;

    protected $fe;

    public function __construct()
    {
        $this->math = new Math();
    }

    public function getTokens(DocumentInterface $doc, $preweighted = false)
    {
        $ps = new PostingStatistics($doc, $preweighted);
        return $ps->getTokens();
    }

    public function getAllUniqueKeys(array $A, array $B)
    {
        $keysA = array_keys(array_filter($A));
        $keysB = array_keys(array_filter($B));
        return array_unique(array_merge($keysA, $keysB));
    }

    public function getScore(DocumentInterface $document, $term)
    {
        $document->getModel()->getIndex()->setTerm($term);

        return $document->getModel()->getScore($document->getTf($term), $document->getDocumentLength(), $document->getNumberOfUniqueTerms());
    }

    public function getDocumentConstant(DocumentInterface $document)
    {
        return $document->getModel()->getDocumentConstant($document->getDocumentLength(), $document->getNumberOfUniqueTerms());
    }


}
