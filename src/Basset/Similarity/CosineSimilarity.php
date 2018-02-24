<?php

namespace Basset\Similarity;

use Basset\Math\Math;

/**
 * Given two vectors compute cos(theta) where theta is the angle
 * between the two vectors in a N-dimensional vector space.
 *
 * cos(theta) = A•B / |A||B|
 */
class CosineSimilarity implements SimilarityInterface
{

    /**
     * Returns a number between 0,1 that corresponds to the cos(theta)
     * where theta is the angle between the two sets if they are treated
     * as n-dimensional vectors.
     *
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function similarity(array $A, array $B)
    {

        $math = new Math();

        $normA = $math->norm($A);
        $normB = $math->norm($B);
        return (($normA * $normB) != 0)
               ? $math->dotProduct($A, $B) / ($normA * $normB)
               : 0;

    }

}
