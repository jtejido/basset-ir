<?php

namespace Basset\Similarity;

use Basset\Math\Math;


class SoftCosineSimilarity implements SimilarityInterface, DistanceInterface
{

    /**
     * http://www.cic.ipn.mx/~sidorov/similarity.pdf
     *
     * @param  array $A Either feature vector or simply vector
     * @param  array $B Either feature vector or simply vector
     * @return float The cosinus of the angle between the two vectors
     */

    protected $dist;

    public function __construct($dist = 1)
    {
        $this->dist = $dist;
        $this->math = new Math();
    }


    public function similarity(array $A, array $B)
    {

        $normA = $this->norm($A, $this->dist);
        $normB = $this->norm($B, $this->dist);
        return (($normA * $normB) != 0)
               ? $this->math->dotProduct($A, $B) * $this->dist / ($normA * $normB)
               : 0;

    }

    public function dist(array $A, array $B)
    {
        return 1-$this->similarity($A,$B);
    }

    private function norm(array $vector, $dist) {
        return sqrt($this->math->dotProduct($vector, $vector) * $dist);
    }
}
