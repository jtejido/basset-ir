<?php

namespace Basset\Normalizers;

use Basset\Utils\TransformationInterface;

/**
 * Base Normalizer Class.
 * It ensures you can apply normalization to tokens as a one-off use.
 */
abstract class Normalizer
{


    abstract public function normalize($w);


    /**
     * Apply normalization to all tokens
     */
    public function normalizeAll(array $items)
    {
        return array_map(
            array($this, 'normalize'),
            $items
        );
    }

}
