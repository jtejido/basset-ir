<?php


namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Models\Contracts\WeightedModelInterface;

interface FeatureExtractionInterface
{
    /**
     * A Feature Extraction Representation.
     *
     * @param  DocumentInterface $doc The document for which we are calculating features
     * @return array
     */
    public function getFeature(array $doc, WeightedModelInterface $model = null): array;
}
