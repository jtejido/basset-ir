<?php

namespace Basset\Metric;

class TriangleSectorSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testTriangleSectorSimilarity()
    {
        $sim = new TriangleSectorSimilarity;
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $B = array("uncle" => 5,"tim" => 6,"and" => 7,"mark" => 8);
        $e = array();
        
        $this->assertEquals(
            0,
            $sim->similarity($A,$B),
            "The similarity of a dissimilar set is 0"
        );

        $this->assertEquals(
            0,
            $sim->similarity($A,$A),
            "The similarity of a set with itself is 0"
        );

        $this->assertEquals(
            0,
            $sim->similarity($A,$e),
            "The similarity of any set with the empty set is 0"
        );

    }
}