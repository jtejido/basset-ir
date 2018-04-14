<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

interface SimilarityInterface
{
    public function similarity(DocumentInterface $q, DocumentInterface $doc);
}
