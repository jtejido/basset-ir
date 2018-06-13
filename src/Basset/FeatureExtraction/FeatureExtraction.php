<?php


namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

/**
 * An object that simply precounts tokens.
 * 
 * @see DocumentInterface
 *
 * @example
 * $fe = new FeatureExtraction();
 * $fe->getFeature($doc);
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class FeatureExtraction implements FeatureExtractionInterface
{

    /**
     * @param  DocumentInterface $doc
     * @return array
     */
    public function getFeature(DocumentInterface $doc): array
    {
    	$tokens = array_count_values($doc->getDocument());    
        return $tokens;
    }

}