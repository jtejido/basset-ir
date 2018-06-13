<?php


namespace Basset\Metric;


/**
 * A Generalization of Jaccard Index and Dice Similarity.
 *
 * The similarity returned by this algorithm is a number between 0,1 The
 * algorithm described in
 * @see http://www.cogsci.ucsd.edu/~coulson/203/tversky-features.pdf, which
 * generalizes both Dice similarity and Jaccard index, does not meet the
 * criteria for a similarity metric (due to its inherent assymetry), but has
 * been made symmetrical as applied here (by Jimenez, S., Becerra, C., Gelbukh,
 * A.): 
 * @see http://aclweb.org/anthology/S/S13/S13-1028.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class TverskyIndex extends Metric implements VSMInterface, SimilarityInterface
{

    CONST ALPHA = 0.5;

    CONST BETA = 1;

    protected $alpha;

    protected $beta;


    /**
     * @param $alpha Set to 0.5 to get either Jaccard Index or Dice Similarity
     * @param $beta  Set to 1 to get Dice Similarity and 2 for Jaccard Index
     */
    public function __construct($alpha = self::ALPHA, $beta = self::BETA)
    {
        parent::__construct();
        $this->alpha = $alpha;
        $this->beta = $beta;
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
        
        $a = array_fill_keys($a,1);
        $b = array_fill_keys($b,1);

        $min = min(count(array_diff_key($a,$b)),count(array_diff_key($b, $a)));
        $max = max(count(array_diff_key($a,$b)),count(array_diff_key($b, $a)));

        $intersect = count(array_intersect_key($a,$b));

        return $intersect/($intersect + ($this->beta * ($this->alpha * $min + $max*(1-$this->alpha)) ));
    }

}
