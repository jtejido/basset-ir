<?php

namespace Basset\FeatureExtraction;


interface FeatureExtractionInterface
{
    /**
     * A Feature Extraction Representation.
     * @return array
     */
    public function getFeature(): array;
}
