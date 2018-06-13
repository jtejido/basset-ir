<?php

namespace Basset\Metric;

use Basset\Math\Math;

class EuclideanDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testEuclideanDistance()
    {

        $sim = new EuclideanDistance();
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $e = array();

        try {
            $sim->dist(
                $A,
                $e
            );
        } catch (\InvalidArgumentException $er) {
            $this->assertEquals(
                'Vector $b is not an array',
                $er->getMessage()
            );
        }

        $this->assertEquals(
            0,
            $sim->dist($A,$A),
            "The distance of a set with itself is 0"
        );

    }
}