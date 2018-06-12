<?php

namespace Basset\Metric;

use Basset\Math\Math;

class EuclideanDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testEuclideanDistance()
    {
        $math = new Math;
        $sim = new EuclideanDistance();
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $e = array();

        $this->assertEquals(
            $math->norm($A),
            $sim->dist($A,$e),
            "The distance of any set with the empty set is simply it's euclidean norm ||x|| = sqrt(x・x)"
        );

        $this->assertEquals(
            0,
            $sim->dist($A,$A),
            "The distance of a set with itself is 0"
        );

    }
}