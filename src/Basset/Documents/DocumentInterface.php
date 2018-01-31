<?php

namespace Basset\Documents;

use Basset\Utils\TransformationInterface;

/**
 * The Document object representation.
 */
interface DocumentInterface
{
    /**
     * Return the document.
     *
     * @return mixed
     */
    public function getDocument();

    /**
     * Apply the transformation to the document.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform);
}
