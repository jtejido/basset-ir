<?php

declare(strict_types=1);

namespace Basset\Index;

use Basset\Utils\Serializer;
use Basset\Structure\{
        TrieManager, 
        Trie
    };


/**
 * The IndexReader simply extracts and reads an index(.idx) from a given path.
 * It also prepares the index to be built on a Trie structure for faster traversal.
 * At the moment we wouldn't allow deleting and/or appending anything from the index. Thus, all new docs you wish to
 * add means you have to rebuild the index thru IndexWriter Class.
 */
    
class IndexReader
{

    CONST EXTENSION = 'idx';

    CONST DEFAULT_FILENAME = 'basset_index';

    CONST DEFAULT_DIRECTORY = '../index/';

    CONST CONFIG_FILE = '../config/config.ini';

    CONST SEPARATOR = '/';

    private $path;

    private $index;

    private $trieManager;

    private $indexManager;

    public function __construct(string $path = null)
    {

        $this->path = $path;
        
        if($this->path === null) {
            $this->path = self::DEFAULT_DIRECTORY . self::DEFAULT_FILENAME . '.' . self::EXTENSION;
        } else {
            if(pathinfo($this->path, PATHINFO_EXTENSION) !== self::EXTENSION) {
                throw new \Exception('Not a valid index file');
            }
        }

        if(!file_exists($this->path)) {
            throw new \Exception('Index File not found. If you have set a custom path and/or filename during IndexWriter, make sure you set its location as Parameter.');
        }

        $this->index = $this->readFile($this->path);

        if(!$this->index instanceof IndexInterface){
            throw new \Exception('Index File not valid. Should be an instance of Basset\Index\IndexInterface');
        }

        $this->indexManager = new IndexManager($this->index);

        $this->trieManager = new TrieManager(new Trie);

        $this->readIndex($this->index);

    }

    public function getIndex(): IndexInterface 
    {
        return $this->index;
    }

    private function readFile(string $path = self::FILENAME): IndexInterface 
    {
        $configDirectory = self::CONFIG_FILE;

        if(!file_exists($configDirectory)) {
            throw new \Exception("Config file not found.");
        }

        $ini = parse_ini_file($configDirectory);

        $hash = new Serializer;
        if(isset($ini['secret_key'])){
            if($file = $hash->unserialize(file_get_contents($path), $ini['secret_key'])){
                return $file;
            } else {
                throw new \Exception("Hash doesn't match. Incorrect Key or corrupted Index file.");
            }
            
        } else {
            throw new \Exception("Private key not set.");
        }

    }

    private function readIndex(IndexInterface $index): bool 
    {

        $data = $index->getData();

        foreach($data as $term => $meta) {
            $this->trieManager->addEntry((string) $term, $meta);
        }

        return true;
    }

    public function getTrieManager(): TrieManager 
    {

        return $this->trieManager;
    }

    public function getIndexManager(): IndexManager 
    {

        return $this->indexManager;
    }



}
