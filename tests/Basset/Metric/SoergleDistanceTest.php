<?php

namespace Basset\Metric;

class SoergleDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testSoergleDistance()
    {
        $sim = new SoergleDistance();
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $e = array();
        
        $this->assertEquals(
            0,
            $sim->dist($A,$A),
            "The distance of a set with itself is 0"
        );

        $this->assertEquals(
            0,
            $sim->dist($A,$e),
            "The distance of any set with the empty set is 0"
        );

    }
}