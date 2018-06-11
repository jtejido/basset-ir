<?php

declare(strict_types=1);

namespace Basset\Index;

use Basset\Math\Math;
use Basset\Collections\CollectionSet;
use Basset\FeatureExtraction\FeatureExtraction;
use Basset\Utils\TransformationInterface;
use Basset\Statistics\{
        EntryStatistics, 
        CollectionStatistics,
        PostingStatistics
    };
use Basset\Structure\{
        TrieManager, 
        TrieInterface,
        Trie
    };


/**
 * The IndexManager manages all operations relating to an Index object.
 * While keeping the Index lightweight, moving the operations here instead of putting it on index makes it safer
 * by not exploiting the methods for code injection.
 * At the moment we wouldn't allow deleting and/or appending anything from the index. Thus, all new docs you wish to
 * add means you have to rebuild the index thru IndexWriter Class.
 * 
 * @see TrieManager
 * @see TrieInterface
 * @see Trie
 * @see CollectionSet
 * @see FeatureExtraction
 * @see EntryStatistics
 * @see CollectionStatistics
 * @see PostingStatistics
 * @see Math
 * @see TransformationInterface
 *
 * @var $set
 * @var $fe
 * @var $index
 * @var $transformer
 *
 * @example 
 * $manager = new IndexManager();
 * $manager->setCollection($collectionset);
 * $manager->setTransformer($transformer);
 * $manager->start();
 * $manager->getData();
 * $manager->search('hello');
 * $manager->getDocuments();
 * $manager->getCollectionStatistics();
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class IndexManager
{

    private $set;

    private $fe;

    private $index;

    private $transformer;

    /**
     * Initializes properties.
     * It takes an IndexInterface type for reading.
     *
     * @param IndexInterface $index The index.
     */
    public function __construct(IndexInterface $index = null)
    {
        $this->fe = null;
        $this->transformer = null;
        $this->index = $index;
    }

    /**
     * This starts the counting operation, once all documents are added and collection is set as the $set property.
     */
    public function start()
    {
        $this->index = new Index;

        $numberofDocuments = 0;

        $totalByTermPresence = array();

        $uniqueTotalByTermPresence = array();

        $termFrequency = array();

        $documentFrequency = array();

        $postinglist = array();

        if ($this->fe === null){
            $this->fe = new FeatureExtraction();
        }

        foreach ($this->set as $class=>$doc) {
            $flag = array();
            $numberofDocuments++;
            $tokens = $this->fe->getFeature($doc);
            $tokensCount = count($tokens);
            $tokensSum = array_sum($tokens);

            foreach ($tokens as $term => $value) {

                if(!isset($postinglist[$term][$class])) {
                    $postinglist[$term][$class] = $value;
                }

                if(!isset($postinglist[$class])) {
                    $postinglist[$term][$class] = $value;
                }

                $flag[$term] = isset($flag[$term]) && $flag[$term] === true ? true : false;

                if (isset($termFrequency[$term])){
                    $termFrequency[$term]+=$value;
                } else {
                    $termFrequency[$term] = $value;
                }

                if (isset($totalByTermPresence[$term])){
                    if ($flag[$term] === false){
                        $flag[$term] = true;
                        $totalByTermPresence[$term] += $tokensSum;
                        $uniqueTotalByTermPresence[$term] += $tokensCount;
                        $documentFrequency[$term]++;
                    }
                } else {
                    $flag[$term] = true;
                    $totalByTermPresence[$term] = $tokensSum;
                    $uniqueTotalByTermPresence[$term] = $tokensCount;
                    $documentFrequency[$term] = 1;
                }

            }
            
        }

        $collectionStats = new CollectionStatistics();
        $collectionStats->setNumberOfDocuments($numberofDocuments);
        $collectionStats->setAverageDocumentLength(array_sum($termFrequency)/$numberofDocuments);
        $collectionStats->setNumberOfTokens(array_sum($termFrequency));
        $collectionStats->setNumberOfUniqueTokens(count($termFrequency));
        $this->index->setCollectionStatistics($collectionStats);

        foreach($termFrequency as $term => $value) {
            $entry = new EntryStatistics();
            $entry->setTermFrequency($value);
            $entry->setDocumentFrequency($documentFrequency[$term]);
            $entry->setTotalByTermPresence($totalByTermPresence[$term]);
            $entry->setUniqueTotalByTermPresence($uniqueTotalByTermPresence[$term]);
            foreach($postinglist[$term] as $class => $value) {
                $post = new PostingStatistics;
                $post->setTf($value);
                $entry->setPostingList($class, $post);
            }

            $this->index->addEntry((string) $term, $entry);
        }

    }

    /**
     * @param CollectionSet $set The document set.
     */
    public function setCollection(CollectionSet $set)
    {
        $this->set = $set;
    }

    /**
     * @param TransformationInterface $transform The transformation to be applied to all documents.
     */
    public function setTransformer(TransformationInterface $transform)
    {
        $this->transformer = $transform;
    }

    /**
     * @param FeatureExtractionInterface $fe The feature extraction to be used for all terms.
     */
    public function setFeature(FeatureExtractionInterface $fe)
    {
        $this->fe = $fe;
    }

    /**
     * @param string $key The key to search for in the index.
     * @return string|null.
     */
    public function search(string $key): ?IndexEntry
    {
        if($this->index === null){
            throw new \Exception('Index not set.');
        }

        return $this->index->getData()[$key] ?? null;
    }

    /**
     * Returns an arranged array of labeled documents.
     * @return array.
     */
    public function getDocuments(): array
    {
        $arrayclass = array();
        foreach($this->index->getData() as $term => $sub) {
            $array = $sub->getValue()->getPostingList();
            foreach($array as $class => $value) {
                    $document[$class][$term] = $value->getTf();
            }
        }
        return $document;
    }

    /**
     * @return CollectionStatistics.
     */
    public function getCollectionStatistics(): CollectionStatistics 
    {
        if($this->index === null){
            throw new \Exception('Index not set.');
        }

        return $this->index->getCollectionStatistics();
    }

    /**
     * @return IndexInterface.
     */
    public function getData(): IndexInterface 
    {
        if($this->index === null){
            throw new \Exception('Index not set.');
        }

        return $this->index;
    }


}
