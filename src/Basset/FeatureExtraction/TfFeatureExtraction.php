<?php
namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Statistics\Statistics;
 
class TfFeatureExtraction implements FeatureExtractionInterface
{
 	protected $stats;

    public function getFeature(DocumentInterface $doc)
    {

    	$tokens = array_count_values($doc->getDocument());

        return $tokens;
    }

}