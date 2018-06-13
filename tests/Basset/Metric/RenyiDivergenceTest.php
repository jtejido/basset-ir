<?php

namespace Basset\Metric;

use Basset\Metric\KLDivergence;

class RenyiDivergenceTest extends \PHPUnit_Framework_TestCase
{
    public function testRenyiDivergence()
    {
        $sim = new RenyiDivergence();
        $A = array("my" => 1,"name" => 2,"is" => 3,"john" => 4);
        $e = array();

        $this->assertEquals(
            2.302585092994046,
            $sim->similarity($A,$A),
            "The similarity of a set with itself is 2.302585092994046"
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
        
        $sim = new RenyiDivergence(1);
        $klDivergence = new KLDivergence;

        $this->assertEquals(
            $klDivergence->dist($A,$A),
            $sim->similarity($A,$A),
            "The special case of Renyi Divergence is KL Divergence"
        );
    }
}