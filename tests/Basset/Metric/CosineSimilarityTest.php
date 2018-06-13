<?php
namespace Basset\Metric;

class CosineSimilarityTest extends \PHPUnit_Framework_TestCase
{
    public function testSetSimilarity()
    {
        $sim = new CosineSimilarity();
        $A = array('a' => 1, 'b' => 2, 'c' => 3);
        $A_times_2 = array('a' => 1,'b' => 2,'c' => 3,'a' => 1,'b' => 2,'c' => 3);
        $B = array('a' => 1, 'b' => 2, 'c' => 3,'d' => 4, 'e' => 5, 'f' => 6);
        $this->assertEquals(
            1,
            $sim->similarity($A,$A),
            "The cosine similarity of a set/vector with itself should be 1"
        );
        $this->assertEquals(
            1,
            $sim->similarity($A,$A_times_2),
            "The cosine similarity of a vector with a linear combination of itself should be 1"
        );
        $this->assertEquals(
            0,
            $sim->similarity($A,$B)-$sim->similarity($A_times_2,$B),
            "Parallel vectors should have the same angle with any vector B"
        );
    }
    public function testProducedAngles()
    {
        $sim = new CosineSimilarity();
        $bba = array('a'=>2,'b'=>4);
        $bbc = array('a'=>3,'b'=>2);
        $ba_to_bc = cos(0.5191461142); // approximately 30 deg

        $this->assertEquals(
            $ba_to_bc,
            $sim->similarity($bba,$bbc)
        );
    }
    public function testZero()
    {
        $sim = new CosineSimilarity();
        $A = array('a' => 1);
        $e = array();

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