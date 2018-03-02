<?php
namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;

 
class LemurTfIdfFeatureExtraction implements FeatureExtractionInterface
{
    protected $stats;

    protected $b;

    protected $k;
  
    const B = 0.75;

    const K = 1.2;

    public function __construct($k = self::K, $b = self::B)
    {
        $this->b = $b;
        $this->k = $k;
    }

    public function setIndex(CollectionStatistics $stats)
    {
        $this->stats = $stats;
        return $this;
    }
 
    public function getFeature(DocumentInterface $doc)
    {
        if(!$this->stats){
            throw new \Exception('Index should be set.');
        }
        
        $documentfrequencies = $this->stats->getDocumentFrequencies();
        $numberofdocuments = $this->stats->getNumberOfDocuments();
        $avg_dl = $this->stats->getAverageDocumentLength();

        $length = count($doc->getDocument());
        $tokens = array_count_values($doc->getDocument());
        foreach ($tokens as $key=>&$value) {
               $value = ($value != 0) ? (($value * $this->k) / ($value + $this->k * (1 - $this->b + $this->b * ($length / $avg_dl)))) * log($numberofdocuments/$documentfrequencies[$key]) : 0;
        }

        return $tokens;
    }


}