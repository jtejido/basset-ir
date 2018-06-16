<?php

namespace Basset\Collections;

use Basset\Documents\TokensDocument;
use Basset\Documents\Document;
use Basset\Utils\TransformationInterface;

/**
 * An array object that wraps a collection of a type TokensDocument.
 * 
 * @see TokensDocument
 * @see TransformationInterface
 *
 * @var $documents
 * @var $labeled
 * @var $currentDocument
 *
 * @example 
 * $collection = new CollectionSet();
 * $collection->addDocument($document, 'label');
 * $collection->applyTransformation($transformers);
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class CollectionSet implements CollectionInterface
{

    const CLASS_AS_KEY = 1;

    const OFFSET_AS_KEY = 2;

    protected $documents;

    protected $labeled;

    protected $currentDocument;
    
    /**
     * While it is always helpful to know which document is which, I will not make it required, as this isn't helpful
     * if you only have a single document collection you simply wanted to compare against your query.
     * 
     * @param bool $labeled OPTIONAL default is false
     */
    public function __construct(bool $labeled = false)
    {
        $this->documents = array();
        $this->labeled = ($labeled == true) ? self::CLASS_AS_KEY : self::OFFSET_AS_KEY;
    }

    /**
     * Adds a document to the set. It does an internal check to see if it is labeled. If not, it will use its offset
     * as key. if CollectionSet is instantiated as true, then it will throw an error.
     *
     * @throws Exception
     * @param mixed $class
     * @param TokensDocument $d
     */
    public function addDocument(TokensDocument $d, $class = null)
    {
        if((empty($class)) && $this->labeled == self::CLASS_AS_KEY) {
            throw new \Exception('Class or Label cannot be null.');
        }
        $class = $class === null ? count($this->documents) : $class;
        $this->documents[] = new Document($d, $class);

    }

    /**
     * Apply the transformation to each documents.
     *
     * @param TransformationInterface $transform
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        foreach ($this->documents as $doc) {
            $doc->applyTransformation($transform);
        }
    }

    public function rewind()
    {
        reset($this->documents);
        $this->currentDocument = current($this->documents);
    }

    public function next()
    {
        $this->currentDocument = next($this->documents);
    }

    public function valid()
    {
        return $this->currentDocument!=false;
    }

    public function current()
    {
        return $this->currentDocument;
    }

    public function key()
    {
        switch ($this->labeled) {
            case self::CLASS_AS_KEY:
                return $this->currentDocument->getClass();
            case self::OFFSET_AS_KEY:
                return key($this->documents);
            default:
                throw new \Exception('Undefined type as key');
        }
    }

    public function offsetSet($key,$value)
    {
        throw new \Exception('Shouldn\'t add documents this way, add them through addDocument()');
    }

    public function offsetUnset($key)
    {
        throw new \Exception('Cannot unset any document');
    }

    public function offsetGet($key)
    {
        return $this->documents[$key];
    }

    public function offsetExists($key)
    {
        return isset($this->documents[$key]);
    }

    public function count()
    {
        return count($this->documents);
    }
}
