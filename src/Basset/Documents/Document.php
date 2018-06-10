<?php

declare(strict_types=1);

namespace Basset\Documents;

use Basset\Utils\TransformationInterface;


class Document implements DocumentInterface
{

    private $d;

    private $class;

    /**
     * @param string $class
     * @param DocumentInterface $d
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
