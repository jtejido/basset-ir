<?php

namespace Basset\Models;

use Basset\Statistics\EntryStatistics;
use Basset\Statistics\CollectionStatistics;
use Basset\Index\IndexInterface;
use Basset\Models\Normalization\NormalizationInterface;
use Basset\Models\AfterEffect\AfterEffectInterface;
use Basset\Math\Math;

abstract class WeightedModel
{

    private $cs;

    private $es;

    protected $math;

    CONST E = M_E;

    public function __construct()
    {
        $this->math = new Math();
    }

    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;
        $this->cs = $this->index->getCollectionStatistics();
        $this->es = $this->index->getEntryStatistics();
    }

    public function getIndex()
    {
        return $this->index;
    }


    public function getCollectionStatistics()
    {
        return $this->cs;
    }

    public function getEntryStatistics()
    {
        return $this->es;
    }

    protected function getTermFrequency()
    {
        return $this->es->getTermFrequency();
    }

    protected function getDocumentFrequency()
    {
        return $this->es->getDocumentFrequency();
    }

    protected function getAverageDocumentLength()
    {
        return $this->cs->getAverageDocumentLength();
    }

    protected function getNumberOfTokens()
    {
        return $this->cs->getNumberOfTokens();
    }

    protected function getNumberOfUniqueTerms()
    {
        return $this->cs->getNumberOfUniqueTerms();
    }

    protected function getNumberOfDocuments()
    {
        return $this->cs->getNumberOfDocuments();
    }

    protected function getTotalByTermPresence()
    {
        return $this->es->getTotalByTermPresence();
    }

    protected function getUniqueTotalByTermPresence()
    {
        return $this->es->getUniqueTotalByTermPresence();
    }

    protected function getDocsByTermPresence()
    {
        return $this->es->getDocsByTermPresence();
    }

    public function getScore($tf, $docLength, $docUniqueLength)
    {
        return $this->score($tf, $docLength, $docUniqueLength);
    }


}