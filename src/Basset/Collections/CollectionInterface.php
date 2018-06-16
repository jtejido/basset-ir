<?php

namespace Basset\Collections;

use Basset\Documents\TokensDocument;
use Basset\Utils\TransformationInterface;

/**
 * The CollectionSet representation.
 */
interface CollectionInterface extends \Iterator,\ArrayAccess,\Countable
{
    /**
     * Adds a document to the set. It does an internal check to see if it is labeled. If not, it will use its offset
     * as key. if CollectionSet is instantiated as true, then it will throw an error.
     *
     * @throws Exception
     * @param mixed $class
     * @param TokensDocument $d
     */
    public function addDocument(TokensDocument $d, $class);

    /**
     * Apply the transformations to each documents.
     *
     * @param TransformationInterface $transform
     */
    public function applyTransformation(TransformationInterface $transform);

}
