<?php


namespace Basset\Index;

use Basset\Utils\TransformationInterface;
use Basset\Collections\CollectionSet;
use Basset\Documents\TokensDocument;
use Basset\Statistics\CollectionStatistics;
use Basset\Utils\Serializer;

/**
 * The IndexWriter creates an index file(.idx) on a given directory.
 * It allows addition of documents as long as the writer is open. Once close() is called, it will not allow any further
 * processing and you have to restart an instance of IndexWriter to rebuilt an index.
 * All commits are done when close() is called. e.g. all indexing, transforming and writing processes.
 * 
 * @see TrieManager
 * @see TrieCollection
 * @see EntryStatistics
 * @see CollectionStatistics
 * @see TokensDocument
 *
 * @var $indexReader
 * @var $trieManager
 * @var $indexManager
 *
 * @example 
 * $index = new IndexWriter('../custom_index'); 
 * $index->setFileName('mycustomindex'); // set filename and directory
 * OR
 * $index = new IndexWriter();
 * $index->setFileName('mycustomindex'); // just set filename but leave at default directory
 * OR
 * $index = new IndexWriter();
 * $index->addDocument(new TokensDocument(array('how', 'do', 'you', 'do?')), 'my doc'); // default to ../index/basset_index.idx
 * $index->applyTransformation($transform);
 * $index->close();
 * $index->getLocation();
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class IndexWriter
{

    CONST EXTENSION = 'idx';

    CONST DEFAULT_FILENAME = 'basset_index';

    CONST DEFAULT_DIRECTORY = __DIR__.'/../../../index/';

    CONST CONFIG_FILE = __DIR__.'/../../../config/config.ini';

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

    public function applyTransformation(TransformationInterface $transformer): bool 
    {

        $this->ensureOpen();
        
        $this->transformer = $transformer;

        return true;

    }

    public function setFilename(string $filename): bool 
    {

        $this->ensureOpen();
        
        $this->filename = $filename;

        return true;

    }

    public function addDocument(TokensDocument $d, $class = null): bool 
    {

        $this->ensureOpen();

        $this->documents[] = array('class' => $class, 'document' => $d);

        return true;

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
        $manager->start();
        return $manager->getData();
    }

    private function writeFile(IndexInterface $index, string $filename): ?int
    {

        $this->ensureOpen();

        $filename = $this->directory . '/' . $filename . '.' . self::EXTENSION;

        $configDirectory = self::CONFIG_FILE;

        if(!file_exists($configDirectory)) {
            throw new \Exception("Config file not found.");
        }

        $ini = parse_ini_file($configDirectory);

        $hash = new Serializer;
        if(isset($ini['secret_key'])){
            $file = $hash->serialize($index, $ini['secret_key']);
            return file_put_contents($filename, $file);
            
        } else {
            return null;
        }
    }

    private function is_dir_empty($dir): ?bool
    {
        $this->ensureOpen();

        if (!is_readable($dir)){
            return NULL; 
        } 

        return (count(scandir($dir)) == 2);
    }

    private function ensureOpen(): ?\Exception
    {
        if($this->open === false){
            throw new \Exception('Index files have been commited. Please start a new instance of IndexWriter.');
        } else {
            return null;
        }
    }

}
