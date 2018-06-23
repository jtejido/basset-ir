<?php


namespace Basset\Index;

use Basset\Utils\Serializer;

/**
 * The IndexReader simply extracts and reads an index(.idx) written by IndexWriter to a given or default path.
 * It also prepares the index to be built on a Trie structure for prefix traversal.
 * At the moment we wouldn't allow deleting and/or appending anything from the index. Thus, all new docs you wish to
 * add means you have to rebuild the index thru IndexWriter Class.
 * 
 * @see Serializer
 * @see IndexWriter
 *
 * @var $path
 * @var $index
 * @var $indexManager
 *
 * @example 
 * $indexReader = new IndexReader('../custom_index/mycustomindex.idx');
 * OR
 * $indexReader = new IndexReader(); //reading from the default path at '../index/basset_index.idx'
 * $indexReader->getIndexManager();
 * $indexReader->getIndex();
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
    
class IndexReader
{

    CONST EXTENSION = 'idx';

    CONST DEFAULT_FILENAME = 'basset_index';

    CONST DEFAULT_DIRECTORY = __DIR__.'/../../../index/';

    CONST CONFIG_FILE = __DIR__.'/../../../config/config.ini';

    private $path;

    private $index;

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

        if(!$this->index){
            throw new \Exception('Index File not valid. Should be an instance of Basset\Index\IndexInterface');
        }

        $this->indexManager = new IndexManager($this->getIndex());

    }

    /**
     * @return IndexInterface
     */
    public function getIndex(): IndexInterface 
    {
        return $this->index;
    }

    /**
     * @param string $path OPTIONAL defaults to 'basset_index'.
     */
    private function readFile(string $path = self::DEFAULT_FILENAME): ?IndexInterface 
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
                return null;
            }
            
        } else {
            throw new \Exception("Private key not set.");
            return null;
        }

    }

    /**
     * @return IndexManager
     */
    public function getIndexManager(): IndexManager 
    {
        return $this->indexManager;
    }



}
