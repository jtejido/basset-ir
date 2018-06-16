<?php

namespace Basset\Feature;


interface FeatureInterface
{
    /**
     * A Feature Extraction Representation.
     *
     * @return array
     */
    public function getFeature(): array;
}
