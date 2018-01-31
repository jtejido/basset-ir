<?php

namespace Basset\Collections;

use Basset\Documents\DocumentInterface;
use Basset\Utils\TransformationInterface;

/**
 * The Document representation.
 */
interface CollectionInterface
{
    /**
     * Return the document.
     *
     * @return mixed
     */
    public function addDocument(DocumentInterface $d, $class);

    /**
     * Apply the transformations to the document.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform);

}
