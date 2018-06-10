<?php

declare(strict_types=1);

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
     * @return array
     */
    public function getDocument(): array;

    /**
     * Apply the transformation to the document.
     *
     * @param TransformationInterface
     */
    public function applyTransformation(TransformationInterface $transform);
}
