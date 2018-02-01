<?php

namespace Basset\Collections;

use Basset\Documents\DocumentInterface;
use Basset\Documents\TrainingDocument;
use Basset\Utils\TransformationInterface;

/**
 * A collection of Document objects.
 */
class CollectionSet implements CollectionInterface, \Iterator,\ArrayAccess,\Countable
{

    const CLASS_AS_KEY = 1;

    const OFFSET_AS_KEY = 2;

    protected $documents;

    protected $keytype;

    protected $currentDocument;
    
    public function __construct($keytype = false)
    {
        $this->documents = array();
        $this->keytype = ($keytype == true) ? self::CLASS_AS_KEY : self::OFFSET_AS_KEY;

    }

    /**
     * Add a document to the set.
     *
     * @param $class The document's actual class
     * @param $d The Document
     * @return void
     */
    public function addDocument(DocumentInterface $d, $class = null)
    {
        if((empty($class)) && $this->keytype == self::CLASS_AS_KEY) {
            throw new \Exception('Class or Label cannot be null.');
        }
        $this->documents[] = new TrainingDocument($d, $class);
    }

    /**
     * Apply the transformation to the data of this document.
     * How the transformation is applied (per token, per token sequence, etc)
     * is decided by the implementing classes.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        foreach ($this->documents as $doc) {
            $doc->applyTransformation($transform);
        }
    }


    // ====== Implementation of \Iterator interface =========
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
        switch ($this->keytype) {
            case self::CLASS_AS_KEY:
                return $this->currentDocument->getClass();
            case self::OFFSET_AS_KEY:
                return key($this->documents);
            default:
                throw new \Exception('Undefined type as key');
        }
    }
    // === Implementation of \Iterator interface finished ===

    // ====== Implementation of \ArrayAccess interface =========
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
    // === Implementation of \ArrayAccess interface finished ===

    // implementation of \Countable interface
    public function count()
    {
        return count($this->documents);
    }
}
