<?php

declare(strict_types=1);

namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

class DataAsFeatures implements FeatureExtractionInterface
{
    /**
     * @param  string $class
     * @param  DocumentInterface $d
     * @return array
     */
    public function getFeature(DocumentInterface $d): array
    {
        return $d->getDocument();
    }
}
