<?php

namespace Basset\Collections;

use Basset\Documents\DocumentInterface;
use Basset\Documents\Document;
use Basset\Utils\TransformationInterface;

/**
 * A collection of Document objects.
 */
class CollectionSet implements CollectionInterface, \Iterator,\ArrayAccess,\Countable
{

    const CLASS_AS_KEY = 1;

    const OFFSET_AS_KEY = 2;

    protected $documents;

    protected $keyed;

    protected $currentDocument;
    
    public function __construct($keyed = false)
    {
        $this->documents = array();
        $this->keyed = ($keyed == true) ? self::CLASS_AS_KEY : self::OFFSET_AS_KEY;
    }

    /**
     * Add a document to the set.
     *
     * @param mixed $class
     * @param DocumentInterface $d
     */
    public function addDocument(DocumentInterface $d, $class = null)
    {
        if((empty($class)) && $this->keyed == self::CLASS_AS_KEY) {
            throw new \Exception('Class or Label cannot be null.');
        }
        $class = $class === null ? count($this->documents) : $class;
        $this->documents[] = new Document($d, $class);

    }

    /**
     * Apply the transformation to the data of this document.
     *
     * @param TransformationInterface
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
        switch ($this->keyed) {
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
