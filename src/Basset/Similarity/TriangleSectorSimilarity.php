<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * This is based on Heidarian and Dinneen's Hybrid Geometric Approach for cosine similarity.
 * https://www.computer.org/csdl/proceedings/bigdataservice/2016/2251/00/2251a142.pdf
 *
 * Given two vectors compute ts * ss
 * where ts is the triangle similarity = (|A|·|B| · sin(θ')) /2
 * θ' is acos(cosine_sim(A,B)) + 10
 * where ss is the sector similarity = π · (pow(euclid_distance(A,B) + magnitudeDifference(A,B), 2)) · (θ' / 360)
 */
class TriangleSectorSimilarity extends Similarity implements SimilarityInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns a number between 0,∞
     *
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function similarity(DocumentInterface $q, DocumentInterface $doc)
    {
        $A = $this->getTokens($q, true);
        $B = $this->getTokens($doc, true);
        return $this->triangleSimilarity($A, $B) * $this->sectorSimilarity($A, $B);

    }

    private function triangleSimilarity(array $q, array $doc) {

        $theta = deg2rad($this->theta($q, $doc));
        $triangle_sim = ($this->math->norm($q) * $this->math->norm($doc) * sin($theta)) / 2;

        return $triangle_sim;
    }

    private function theta(array $q, array $doc) {
        $cos = $this->cosineSimilarity($q, $doc);
        $theta = 0;
        if($cos != 0){
            $theta += acos($cos) + 10;
        }

        return $theta;
    }

    private function sectorSimilarity(array $q, array $doc) {

        $sector_sim = pi() * (pow(($this->euclideanDistance($q, $doc) + $this->magnitudeDifference($q, $doc)), 2)) * ($this->theta($q, $doc) / 360);

        return $sector_sim;
    }

    private function magnitudeDifference(array $q, array $doc) {

        $mag_diff = abs($this->math->norm($q) - $this->math->norm($doc));

        return $mag_diff;
    }

    private function euclideanDistance(array $q, array $doc)
    {
        $a = array();
        foreach ($q as $key => $value) {
            $a[$key] = $value;
        }
        foreach ($doc as $key=>$value) {
            if (isset($a[$key]))
                $a[$key] -= $value;
            else
                $a[$key] = $value;
        }

        return sqrt(
            array_sum(
                array_map(
                    function ($x) {
                        return $x*$x;
                    },
                    $a
                )
            )
        );

    }

    private function cosineSimilarity(array $q, array $doc)
    {

        $normA = $this->math->norm($q);
        $normB = $this->math->norm($doc);
        return (($normA * $normB) != 0)
               ? $this->math->dotProduct($q, $doc) / ($normA * $normB)
               : 0;
    }

}
