<?php

namespace Basset\Similarity;

use Basset\Math\Math;
use Basset\Similarity\CosineSimilarity;
use Basset\Similarity\Euclidean;

/**
 * This is based on Heidarian and Dinneen's Hybrid Geometric Approach for cosine similarity.
 * https://www.computer.org/csdl/proceedings/bigdataservice/2016/2251/00/2251a142.pdf
 *
 * Given two vectors compute ts * ss
 * where ts is the triangle similarity = (|A|·|B| · sin(θ')) /2
 * θ' is acos(cosine_sim(A,B)) + 10
 * where ss is the sector similarity = π · (pow(euclid_distance(A,B) + magnitudeDifference(A,B), 2)) · (θ' / 360)
 */
class TriangleSectorSimilarity implements SimilarityInterface
{

    public function __construct()
    {
        $this->math = new Math();
        $this->cos_sim = new CosineSimilarity();
        $this->euc_dist = new Euclidean();
    }

    /**
     * Returns a number between 0,∞ that corresponds to the ts_ss
     *
     * @param  array $A
     * @param  array $B
     * @return float
     */
    public function similarity(array $A, array $B)
    {

        return $this->triangleSimilarity($A, $B) * $this->sectorSimilarity($A, $B);

    }

    private function triangleSimilarity(array $A, array $B) {

        $theta = deg2rad($this->theta($A, $B));
        $triangle_sim = ($this->math->norm($A) * $this->math->norm($B) * sin($theta)) / 2;

        return $triangle_sim;
    }

    private function theta(array $A, array $B) {
        $cos = $this->cos_sim->similarity($A, $B);
        $theta = 0;
        if($cos != 0){
            $theta += acos($cos) + 10;
        }

        return $theta;
    }

    private function sectorSimilarity(array $A, array $B) {

        $sector_sim = pi() * (pow(($this->euc_dist->dist($A, $B) + $this->magnitudeDifference($A, $B)), 2)) * ($this->theta($A, $B) / 360);

        return $sector_sim;
    }

    private function magnitudeDifference(array $A, array $B) {

        $mag_diff = abs($this->math->norm($A) - $this->math->norm($B));

        return $mag_diff;
    }

}
