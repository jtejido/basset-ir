<?php


namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

interface FeatureExtractionInterface
{
    /**
     * A Feature Extraction Representation.
     *
     * @param  DocumentInterface $doc The document for which we are calculating features
     * @return array
     */
    public function getFeature(DocumentInterface $doc): array;
}
