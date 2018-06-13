<?php


namespace Basset\Documents;

use Basset\Utils\TransformationInterface;

/**
 * The Document contract/representation.
 */
interface DocumentInterface
{
    /**
     * Returns the Tokenized Document as received by TokensDocument
     *  
     * @return array
     */
    public function getDocument(): array;

    /**
     * Apply the transform to the document
     *
     * @param TransformationInterface $transform
     */
    public function applyTransformation(TransformationInterface $transform);
}
