<?php

namespace Basset\Metric;

use Basset\Math\Math;

class VectorSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testVectorSimilarity()
    {
        $sim = new VectorSimilarity;
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $B = array("uncle" => 5,"tim" => 6,"and" => 7,"mark" => 8);
        $e = array();
        $math = new Math;
        
        $this->assertEquals(
            0,
            $sim->similarity($A,$B),
            "The similarity of a totally dissimilar set is 0"
        );

        $this->assertEquals(
            $math->dotProduct($A, $A),
            $sim->similarity($A,$A),
            "The similarity of a set with itself is its Dot Product"
        );

        try {
            $sim->similarity($A,$e);
        } catch (\InvalidArgumentException $er) {
            $this->assertEquals(
                'Vector $b is not an array',
                $er->getMessage()
            );
        }

    }
}