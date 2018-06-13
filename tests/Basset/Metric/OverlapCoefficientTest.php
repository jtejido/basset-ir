<?php
namespace Basset\Metric;

class OverlapCoefficientTest extends \PHPUnit_Framework_TestCase
{
    public function testOverlapCoefficient()
    {
        $sim = new OverlapCoefficient();
        $A = array("my","name","is","john");
        $B = array("your","name","is","joe");
        $e = array();
        $this->assertEquals(
            1,
            $sim->similarity($A,$A),
            "The similarity of a set with itself is 1"
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
        $this->assertEquals(
            0.5,
            $sim->similarity($A,$B),
            "similarity({'my','name','is','john'},{'your','name','is','joe'}) = 0.5"
        );
    }
}