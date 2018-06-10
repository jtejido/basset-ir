<?php

declare(strict_types=1);

namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;

class FeatureExtraction implements FeatureExtractionInterface
{

    protected $preweighted;

    public function __construct($preweighted = false)
    {
        $this->preweighted = $preweighted;
    }

    public function getFeature(DocumentInterface $doc): array
    {

    	$tokens = array_count_values($doc->getDocument());

    	if($this->preweighted){
    		foreach($tokens as $key => &$value){
    			$doc->getModel()->getEntryStatistics()->setTerm($key);
    			$value = $doc->getModel()->score($doc->getPostingStats()->getTf($key), $doc->getPostingStats()->getDocumentLength(), $doc->getPostingStats()->getNumberOfUniqueTerms());
    		}
    	}
        
        return $tokens;
    }

}