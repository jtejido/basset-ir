<?php

namespace Basset\Normalizers;

use Basset\Utils\TransformationInterface;

/**
 * For English we simply transform to lower case using mb_strtolower.
 * This should be used as a fallback for any language since mb_strtolower
 * will do at least half good a job
 */
class English extends Normalizer implements TransformationInterface, NormalizerInterface
{

    public function normalize($w)
    {
        return mb_strtolower($w,"utf-8");
    }

    /**
     * Apply transformation
     */
    public function transform($w)
    {
        return $this->normalize($w);
    }

    
}
