<?php
namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;

 
class PivotTfIdfFeatureExtraction implements FeatureExtractionInterface
{
    protected $stats;

    protected $slope;

    const SLOPE = 0.20;
 
    public function __construct($slope = self::SLOPE)
    {
        $this->slope = $slope;
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
             $value = ($value != 0) ?  (1+log(1+log($value))) / ((1-$this->slope) + ($this->slope * ($length / $avg_dl))) * log(($numberofdocuments+1)/$documentfrequencies[$key]) : 0;
        }

        return $tokens;
    }


}