<?php

namespace Basset\Metric;

class CanberraDistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testCanberraDistance()
    {
        $sim = new CanberraDistance();
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $e = array();
        
        $this->assertEquals(
            0,
            $sim->dist($A,$A),
            "The distance of a set with itself is 0"
        );

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

    }
}