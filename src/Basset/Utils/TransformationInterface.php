<?php

declare(strict_types=1);

namespace Basset\Utils;

/**
 * TransformationInterface represents any type of transformation
 * to be applied upon documents.
 */
interface TransformationInterface
{
    /**
     * Return the value transformed.
     * @param  mixed $value The value to transform
     * @return mixed
     */
    public function transform($value);
}
