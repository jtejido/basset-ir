<?php
namespace Basset\Metric;

use Basset\Metric\JaccardIndex;
use Basset\Metric\DiceSimilarity;

class TverskyIndexTest extends \PHPUnit_Framework_TestCase
{
    private function sim($A, $B, $a, $b)
    {
        $sim = new TverskyIndex($a, $b);
        return $sim->similarity($A, $B);
    }
    public function testTverskyIndex()
    {
        $sim = new TverskyIndex();
        $jac = new JaccardIndex();
        $dice = new DiceSimilarity();
        $A = array("my","name","is","john");
        $B = array("my","name","is","joe");
        $C = array(1,2,3);
        $D = array(1,2,3,4,5,6);
        $e = array();
        $this->assertEquals(
            1,
            $this->sim($A,$A, 0.5, 1),
            "The similarity of a set with itsself is 1."
        );

        $this->assertEquals(
            0.75,
            $this->sim($A,$B, 0.5, 1),
            "similarity({'my','name','is','john'},{'my','name','is','joe'}) = 0.75"
        );

        $this->assertEquals(
            $dice->similarity($A,$B),
            $this->sim($A,$B, 0.5, 1),
            "The result at parameters alpha = 0.5 and beta = 2 is the same as DiceSimilarity."
        );

        $this->assertEquals(
            0.5,
            $this->sim($C,$D, 0.5, 2),
            "similarity({1,2,3},{1,2,3,4,5,6}) = 0.5"
        );

        $this->assertEquals(
            $jac->similarity($C,$D),
            $this->sim($C,$D, 0.5, 2),
            "The result at parameters alpha = 0.5 and beta = 2 is the same as JaccardIndex."
        );

        try {
            $this->sim($A,$e, 0.5, 1);
        } catch (\InvalidArgumentException $er) {
            $this->assertEquals(
                'Vector $b is not an array',
                $er->getMessage()
            );
        }
    }
}