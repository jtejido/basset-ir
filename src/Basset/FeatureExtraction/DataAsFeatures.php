<?php
namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

class DataAsFeatures implements FeatureExtractionInterface
{
    /**
     * @param  string            $class The class for which we are calculating features
     * @param  DocumentInterface $d     The document to calculate features for.
     * @return array
     */
    public function getFeature(DocumentInterface $d)
    {
        return $d->getDocument();
    }
}
