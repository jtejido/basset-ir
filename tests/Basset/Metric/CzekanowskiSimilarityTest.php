<?php

namespace Basset\Metric;

class CzekanowskiSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testCzekanowskiSimilarity()
    {
        $sim = new CzekanowskiSimilarity();
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $e = array();

        $this->assertEquals(
            1,
            $sim->similarity($A,$A),
            "The distance of a set with itself is 0"
        );

        try {
            $sim->similarity(
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