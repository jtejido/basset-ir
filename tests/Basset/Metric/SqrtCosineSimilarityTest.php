<?php

namespace Basset\Metric;

class SqrtCosineSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testSqrtCosineSimilarity()
    {
        $sim = new SqrtCosineSimilarity;
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $e = array();
        
        $this->assertEquals(
            1,
            $sim->similarity($A,$A),
            "The similarity of a set with itself is 1"
        );

        $this->assertEquals(
            0,
            $sim->similarity($A,$e),
            "The similarity of any set with the empty set is 0"
        );

    }
}