<?php


namespace Basset\Metric;


/**
 * Given two vectors compute cos(theta) where theta is the angle
 * between the two vectors in a N-dimensional vector space.
 *
 * cos(theta) = A•B / |A||B|
 *
 * @see https://en.wikipedia.org/wiki/Cosine_similarity
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class CosineSimilarity extends Metric implements VSMInterface, SimilarityInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns a number between 0,1 that corresponds to the cos(theta)
     * where theta is the angle between the two sets if they are treated
     * as n-dimensional vectors.
     *
     * @param  array $a
     * @param  array $b
     * @return float
     */
    public function similarity(array $a, array $b): float
    {
        if(empty($a) || empty($b)){
            throw new \InvalidArgumentException('Vector $' . (empty($a) ? 'a' : 'b') . ' is not an array');
        }

        $normA = $this->math->euclideanNorm($a);
        $normB = $this->math->euclideanNorm($b);
        return (($normA * $normB) > 0)
               ? $this->math->dotProduct($a, $b) / ($normA * $normB)
               : 0;

    }

}
