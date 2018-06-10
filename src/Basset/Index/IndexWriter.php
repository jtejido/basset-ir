<?php

declare(strict_types=1);

namespace Basset\Index;

use Basset\Utils\TransformationInterface;
use Basset\Collections\CollectionSet;
use Basset\Documents\DocumentInterface;
use Basset\Statistics\CollectionStatistics;

/**
 * The IndexWriter creates an index file(.idx) on a given directory.
 * It allows addition of documents as long as the writer is open. Once close() is called, it will not allow any further
 * processing and you have to restart an instance of IndexWriter to rebuilt an index.
 * All commits are done when close() is called. e.g. all indexing, transforming and writing processes.
 */

class IndexWriter
{

    CONST EXTENSION = 'idx';

    CONST DEFAULT_FILENAME = 'basset_index';

    CONST DEFAULT_DIRECTORY = '../index/';

    CONST SEPARATOR = '/';

    private $open;

    private $directory;

    private $collectionset;

    private $transformer;

    private $documents;

    public function __construct(string $directory = self::DEFAULT_DIRECTORY, bool $overwrite = true)
    {
        $this->open = true;
        $this->collectionset = new CollectionSet(true);
        $this->transformer = null;
        $this->directory = rtrim($directory,self::SEPARATOR).self::SEPARATOR;
        $this->filename = self::DEFAULT_FILENAME;
        if($this->directory === null) {
            throw new \Exception('Index Directory Path should be set.');
        }
        if(!file_exists($this->directory) && !is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
        if (!$overwrite && is_dir($this->directory)) {
            if(!$this->is_dir_empty($this->directory)) {
                throw new \Exception('Directory not empty. Please delete or move the current index somewhere else.');
            }
        }
    }

    public function applyTransformation(TransformationInterface $transformer) 
    {

        $this->ensureOpen();
        
        $this->transformer = $transformer;

    }

    public function setFilename(string $filename) 
    {

        $this->ensureOpen();
        
        $this->filename = $filename;

    }

    public function addDocument(DocumentInterface $d, $class = null) 
    {

        $this->ensureOpen();

        $this->documents[] = array('class' => $class, 'document' => $d);

    }

    public function getLocation(): string
    {
        return $this->directory . $this->filename . '.' . self::EXTENSION;
    }

    public function close(): bool
    {
        $this->ensureOpen();
        
        foreach($this->documents as $docs) {
            if($docs['class'] === null) {
                $this->collectionset = new CollectionSet();
                break;
            }
        }

        foreach($this->documents as $docs) {
            $this->collectionset->addDocument($docs['document'], $docs['class']);
        }

        if($this->transformer !== null){
            $this->collectionset->applyTransformation($this->transformer);
        }

        if(!$this->writeFile($this->startLexicalIndex($this->collectionset), $this->filename)){
            throw new \Exception('No index file written.');
        }

        $this->open = false;

        return true;
    }

    private function startLexicalIndex(CollectionSet $collectionset): IndexInterface 
    {

        $this->ensureOpen();
        $manager = new IndexManager();
        $manager->setCollection($collectionset);
        $manager->setTransformer($this->transformer);
        $manager->start();
        return $manager->getData();
    }

    private function writeFile(IndexInterface $index, string $filename): int
    {

        $this->ensureOpen();

        $filename = $this->directory . '/' . $filename . '.' . self::EXTENSION;
        return file_put_contents($filename, serialize($index));
    }

    private function is_dir_empty($dir): bool
    {
        $this->ensureOpen();

        if (!is_readable($dir)){
            return NULL; 
        } 

        return (count(scandir($dir)) == 2);
    }

    private function ensureOpen(): void
    {
        if($this->open === false){
            throw new \Exception('Index files have been commited. Please start a new instance of IndexWriter.');
        }
    }

}
