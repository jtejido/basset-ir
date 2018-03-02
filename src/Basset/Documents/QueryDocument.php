<?php

namespace Basset\Documents;

use Basset\Utils\TransformationInterface;


/**
 * Represents the query.
 */
class QueryDocument implements DocumentInterface
{
    protected $query;

    public function __construct(DocumentInterface $d)
    {
        $this->query = $d;
    }

    public function getDocument()
    {
        return $this->query->getDocument();
    }

    /**
     * Apply the transform to each token. Filter out the null tokens.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        $this->query->applyTransformation($transform);
    }
}
