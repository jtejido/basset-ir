<?php

namespace Basset\Ranking;

use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;
use Basset\Statistics\PostingStatistics;
use Basset\Statistics\EntryStatistics;
use Basset\Ranking\Divergence\DFIInterface;
use Basset\Ranking\IDF\IdfInterface;


/**
 * Provides a framework for nonparametric index term weighting model using the notion of independence, as described
 * in Kocabas et al's paper.
 * http://trec.nist.gov/pubs/trec18/papers/muglau.WEB.MQ.pdf
 *
 * DFI models are obtained by instantiating the three components of the framework: 
 * selecting a divergence measure and selecting an idf method, take note that it uses log2 similar to DFR.
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class DFIRanking extends AbstractRanking
{

    protected $dfi;

    protected $idf;

    protected $collectionstats;

    public function __construct(DFIInterface $dfi, IdfInterface $idf, CollectionSet $set)
    {
        parent::__construct($set);
        $this->dfi    = $dfi;
        $this->idf    = $idf;
        $this->collectionstats = new CollectionStatistics($this->set);

        if ($this->dfi == null || $this->idf == null) {
            throw new \Exception("Null Parameters not allowed.");
        }

    }

    /**
     * Returns result ordered by rank.
     *
     * @param  DocumentInterface $q
     * @return array
     */
    public function search(DocumentInterface $q)
    {

        $score = array();
        $term_stats = new PostingStatistics($q); //get query index
        $terms = array_keys($term_stats->getDocument());

        foreach($this->set as $class => $doc) {
            $score[$class] = isset($score[$class]) ? $score[$class] : 0;
            $posting_stats = new PostingStatistics($doc); //get doc index
            $docLength = $posting_stats->getDocumentLength();
            foreach ($terms as $term){
                $entrystats = new EntryStatistics($this->collectionstats, $term);
                $tf = $posting_stats->getTf($term);
                $keyFrequency = $term_stats->getTf($term);
                
                $expected = ($entrystats->getTermFrequency() * $docLength) / $this->collectionstats->getNumberOfTokens();
                
                if($tf > $expected) {

                    if ($this->dfi) {
                        $dfi = $this->dfi->getDFI($tf, $expected);
                    }

                    if ($this->idf) {
                        $this->idf->setCollectionStatistics($this->collectionstats);
                        $this->idf->setEntryStatistics($entrystats);
                        $idf = $this->idf->getIdf();
                    }

                    $score[$class] += ($keyFrequency * $dfi * $idf);
                }
            }
        }

        arsort($score);
        return $score;

    }

}