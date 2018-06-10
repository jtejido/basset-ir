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
     */
    public function addDocument(DocumentInterface $d, $class);

    /**
     * Apply the transformations to the document.
     *
     * @param TransformationInterface
     */
    public function applyTransformation(TransformationInterface $transform);

}
