<?php

declare(strict_types=1);

namespace Basset\Metric;

use Basset\Metric\{
        CosineSimilarity, 
        EuclideanDistance
    };

/**
 * This is based on Heidarian and Dinneen's Hybrid Geometric Approach for cosine similarity.
 * @see https://www.computer.org/csdl/proceedings/bigdataservice/2016/2251/00/2251a142.pdf
 *
 * Given two vectors compute ts * ss
 * where ts is the triangle similarity = (|A|·|B| · sin(θ')) /2
 * θ' is acos(cosine_sim(A,B)) + 10
 * where ss is the sector similarity = π · (pow(euclid_distance(A,B) + magnitudeDifference(A,B), 2)) · (θ' / 360)
  *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class TriangleSectorSimilarity extends Metric implements VSMInterface, SimilarityInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  array $a
     * @param  array $b
     * @return float
     */
    public function similarity(array $a, array $b): float
    {
        return $this->triangleSimilarity($a, $b) * $this->sectorSimilarity($a, $b);
    }

    private function triangleSimilarity(array $a, array $b) {

        $theta = deg2rad($this->theta($a, $b));
        $triangle_sim = ($this->math->norm($a) * $this->math->norm($b) * sin($theta)) / 2;

        return $triangle_sim;
    }

    private function theta(array $a, array $b) {
        $sim = new CosineSimilarity;
        $cos = $sim->similarity($a, $b);
        $theta = 0;
        if($cos != 0){
            $theta += acos($cos) + 10;
        }

        return $theta;
    }

    private function sectorSimilarity(array $a, array $b) {
        $dist = new EuclideanDistance;
        $sector_sim = pi() * (pow(($dist->dist($a, $b) + $this->magnitudeDifference($a, $b)), 2)) * ($this->theta($a, $b) / 360);

        return $sector_sim;
    }

    private function magnitudeDifference(array $a, array $b) {

        $mag_diff = abs($this->math->norm($a) - $this->math->norm($b));

        return $mag_diff;
    }

}
