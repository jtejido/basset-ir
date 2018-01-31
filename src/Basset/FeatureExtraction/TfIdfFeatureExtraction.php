<?php
namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Statistics\Statistics;

 
class TfIdfFeatureExtraction implements FeatureExtractionInterface
{
    protected $stats;
 

    public function setIndex(Statistics $stats)
    {
        $this->stats = $stats;
        return $this;
    }
 
    public function getFeature(DocumentInterface $doc)
    {
        if(!$this->stats){
            throw new \Exception('Index should be set.');
        }

        $tokens = array_count_values($doc->getDocument());
        foreach ($tokens as $key=>&$value) {
            $value = $value * $this->stats->idf($key);
        }

        return $tokens;
    }


}