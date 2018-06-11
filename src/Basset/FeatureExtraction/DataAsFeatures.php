<?php

declare(strict_types=1);

namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

/**
 * An object that simply returns tokens.
 * @deprecated
 * 
 * @see DocumentInterface
 *
 * @example
 * $fe = new FeatureExtraction();
 * $fe->getFeature($doc);
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class DataAsFeatures implements FeatureExtractionInterface
{
    /**
     * @param  DocumentInterface $doc
     * @return array
     */
    public function getFeature(DocumentInterface $doc): array
    {
        return $doc->getDocument();
    }
}
