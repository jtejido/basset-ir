<?php


namespace Basset\Metric;


/**
 * @see http://en.wikipedia.org/wiki/Sørensen–Dice_coefficient
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class DiceSimilarity extends Metric implements VSMInterface, SimilarityInterface
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * @param  array $a
     * @param  array $b
     * @return float
     */
    public function similarity(array $a, array $b): float
    {

        if(empty($a) || empty($b)){
            throw new \InvalidArgumentException('Vector $' . (empty($a) ? 'a' : 'b') . ' is not an array');
        }
        
        $A = array_fill_keys($a,1);
        $B = array_fill_keys($b,1);

        $intersect = count(array_intersect_key($A,$B));
        $a_count = count($A);
        $b_count = count($B);

        return (2*$intersect)/($a_count + $b_count);
    }

}