<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

interface DistanceInterface
{
    public function dist(DocumentInterface $q, DocumentInterface $doc);
}
