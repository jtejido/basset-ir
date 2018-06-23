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

    protected $documents;

    protected $currentDocument;
    
    public function __construct()
    {
        $this->documents = array();
    }

    /**
     * Adds a document with metadata to the set.
     *
     * @param TokensDocument $d
     * @param MetaData $metadata
     */
    public function addDocument(TokensDocument $d, $metadata = null)
    {
        $this->documents[] = new Document($d, $metadata);
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
        return key($this->documents);
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
