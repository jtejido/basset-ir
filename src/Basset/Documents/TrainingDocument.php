<?php

namespace Basset\Documents;

use Basset\Utils\TransformationInterface;

/**
 * A Document object that have known label/class with it.
 */
class TrainingDocument implements DocumentInterface
{
    protected $d;
    protected $class;

    /**
     * @param string            $class The actual label/class of the Document $d
     * @param DocumentInterface $d     The document to be processed
     */
    public function __construct(DocumentInterface $d, $class = null)
    {
        $this->d = $d;
        $this->class = $class;
    }
    public function getDocument()
    {
        return $this->d->getDocument();
    }
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Apply the transform to the document.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        $this->d->applyTransformation($transform);
    }
}
