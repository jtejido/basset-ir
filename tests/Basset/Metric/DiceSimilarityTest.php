<?php
namespace Basset\Metric;

class DiceSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testDiceSimilarity()
    {
        $sim = new DiceSimilarity();
        $A = array("my","name","is","john");
        $B = array("my","name","is","joe");
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
            0.75,
            $sim->similarity($A,$B),
            "similarity({'my','name','is','john'},{'my','name','is','joe'}) = 0.75"
        );
    }
}