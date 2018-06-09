<?php

namespace Basset\Documents;

use Basset\Utils\TransformationInterface;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Statistics\PostingStatistics;


class Document implements DocumentInterface
{

    private $d;

    private $class;

    /**
     * @param string            $class The actual label/class of the Document $d
     * @param DocumentInterface $d     The document to be processed
     */
    public function __construct(DocumentInterface $d, $class = null)
    {
        $this->d = $d;
        $this->class = ($class === null) ? 0 : $class;       
    }

    public function getDocument(): array
    {
        return $this->d->getDocument();
    }

    public function getClass()
    {
        return $this->class;
    }

    /**
     * Apply the transform to the document. Re-compute token stats.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        $this->d->applyTransformation($transform);
    }
}
