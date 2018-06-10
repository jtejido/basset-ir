<?php

declare(strict_types=1);

namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

interface FeatureExtractionInterface
{
    /**
     * Text Analysis is a major application field for machine learning algorithms. However, the raw data, a sequence of 
     * symbols, cannot be fed directly to the algorithms themselves as most of them expect numerical feature vectors with a 
     * fixed size rather than the raw text documents with variable length.
     *
     * @param  string $class The class for which we are calculating features
     * @param  DocumentInterface $d The document for which we are calculating features
     * @return array
     */
    public function getFeature(DocumentInterface $doc): array;
}
