<?php
namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Statistics\Statistics;

 
class LemurTfIdfFeatureExtraction implements FeatureExtractionInterface
{
    protected $stats;
  
    const B = 0.75;

    const K = 1.2;

    public function __construct($k = self::K, $b = self::B)
    {
        $this->b = $b;
        $this->k = $k;
    }

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
        
        $length = count($doc->getDocument());
        $numberofTokens = $this->stats->numberofCollectionTokens();
        $avg_dl = $length/$numberofTokens;
        $tokens = array_count_values($doc->getDocument());
        foreach ($tokens as $key=>&$value) {
               $value = ($value != 0) ? (($value * $this->k) / ($value + $this->k * (1 - $this->b + $this->b * ($length / $avg_dl)))) * $this->stats->idf($key) : 0;
        }

        return $tokens;
    }


}