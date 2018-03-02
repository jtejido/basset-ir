<?php
namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;

 
class TfIdfFeatureExtraction implements FeatureExtractionInterface
{
    protected $stats;
 

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

        $tokens = array_count_values($doc->getDocument());
        foreach ($tokens as $key=>&$value) {
            $value = $value * log($numberofdocuments/$documentfrequencies[$key]);
        }

        return $tokens;
    }


}