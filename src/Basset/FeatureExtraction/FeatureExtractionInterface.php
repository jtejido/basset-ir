<?php


namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

interface FeatureExtractionInterface
{
    /**
     * A Feature Extraction Representation.
     * @param  array $doc
     * @return array
     */
    public function getFeature(array $doc): array;
}
