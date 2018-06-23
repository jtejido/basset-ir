<?php


namespace Basset\Index;

use Basset\MetaData\MetaData;
use Basset\Math\Math;
use Basset\Collections\CollectionSet;
use Basset\Feature\FeatureVector;
use Basset\Utils\TransformationInterface;
use Basset\Statistics\{
        EntryStatistics, 
        CollectionStatistics,
        PostingStatistics
    };


/**
 * The IndexManager manages all operations relating to an Index object.
 * While keeping the Index lightweight, moving the operations here instead of putting it on index makes it safer
 * by not exposing the methods.
 * At the moment we wouldn't allow deleting and/or appending anything from the index. Thus, all new docs you wish to
 * add means you have to rebuild the index thru a new IndexWriter instance.
 * 
 * @see CollectionSet
 * @see EntryStatistics
 * @see CollectionStatistics
 * @see PostingStatistics
 * @see Math
 *
 * @var $set
 * @var $index
 *
 * @example 
 * $manager = new IndexManager();
 * $manager->setCollection($collectionset);
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

    /**
     * Initializes properties.
     * It takes an IndexInterface type for reading.
     *
     * @param IndexInterface $index The index.
     */
    public function __construct(IndexInterface $index = null)
    {
        $this->set = null;
        $this->index = $index;
    }

    /**
     * This starts the counting operation, once all documents are added and collection is set as the $set property.
     */
    public function start()
    {

        if($this->set === null){
            throw new \Exception('Collection not set.');
        }

        $this->index = new Index;

        $numberofDocuments = 0;

        $totalByTermPresence = array();

        $uniqueTotalByTermPresence = array();

        $termFrequency = array();

        $documentFrequency = array();

        $postinglist = array();

        foreach ($this->set as $id=>$doc) {
            
            $this->index->addMetaData($id, $doc->getMetaData());

            $flag = array();
            $numberofDocuments++;
            $tokens = array_count_values($doc->getDocument());
            $tokensCount = count($tokens);
            $tokensSum = array_sum($tokens);

            foreach ($tokens as $term => $value) {

                if(!isset($postinglist[$term][$id])) {
                    $postinglist[$term][$id] = $value;
                }

                if(!isset($postinglist[$id])) {
                    $postinglist[$term][$id] = $value;
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
            foreach($postinglist[$term] as $id => $value) {
                $post = new PostingStatistics;
                $post->setTf($value);
                $entry->setPostingList($id, $post);
            }

            $this->index->addEntry($term, $entry);
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
     * @param string $key The key to search for in the index.
     * @return EntryStatistics|null.
     */
    public function search(string $key): ?EntryStatistics
    {
        if($this->index === null){
            throw new \Exception('Index not set.');
        }

        return isset($this->index->getData()[$key]) ? $this->index->getData()[$key]->getValue() : null;
    }

    /**
     * Returns an arranged array of labeled documents.
     * @return array.
     */
    public function getDocuments(): array
    {
        $documents = array();
        foreach($this->index->getData() as $term => $sub) {
            $array = $sub->getValue()->getPostingList();
            foreach($array as $id => $value) {
                    $documents[$id][$term] = $value->getTf();
            }
        }
        return $documents;
    }

    /**
     * Returns an array of document vectors.
     * @return array.
     */
    public function getDocumentVectors(): array
    {
        $documents = $this->getDocuments();

        $documentvector = array();
        foreach($documents as $id => $document) {
            $documentvector[$id] = new FeatureVector($document);
        }

        return $documentvector;
    }

    /**
     * Returns metadata of a given docID.
     * @return MetaData.
     */
    public function getMetaData(int $id): MetaData
    {
        return $this->index->getMetaData($id);
    }

    /**
     * Returns document vector by id.
     * @return array.
     */
    public function getDocumentVector(int $id): FeatureVector
    {
        $documents = $this->getDocumentVectors();

        return $documents[$id];
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
