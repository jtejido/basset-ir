<?php

namespace Basset\Similarity;

use Basset\Documents\DocumentInterface;

/**
 * A Generalization of Jaccard Index and Dice Similarity.
 *
 * The similarity returned by this algorithm is a number between 0,1 The
 * algorithm described in
 * http://www.cogsci.ucsd.edu/~coulson/203/tversky-features.pdf, which
 * generalizes both Dice similarity and Jaccard index, does not meet the
 * criteria for a similarity metric (due to its inherent assymetry), but has
 * been made symmetrical as applied here (by Jimenez, S., Becerra, C., Gelbukh,
 * A.): http://aclweb.org/anthology/S/S13/S13-1028.pdf
 */
class TverskyIndex extends Similarity implements SimilarityInterface
{

    CONST ALPHA = 0.5;

    CONST BETA = 1;

    protected $alpha;

    protected $beta;


    /**
     * @param $alpha Set to 0.5 to get either Jaccard Index or Dice Similarity
     * @param $beta  Set to 1 to get Jaccard Index and 2 for Dice Similarity
     */
    public function __construct($alpha=self::ALPHA, $beta=self::BETA)
    {
        parent::__construct();
        $this->alpha = $alpha;
        $this->beta = $beta;
    }

    /**
     * Compute the similarity using the alpha and beta values given in the
     * constructor.
     * @param  QueryDocument $q
     * @param  Document $doc
     * @return float
     */
    public function similarity(DocumentInterface $q, DocumentInterface $doc)
    {
        $A = $this->getTokens($q, true);
        $B = $this->getTokens($doc, true);
        $alpha = $this->alpha;
        $beta = $this->beta;

        $a = array_fill_keys($A,1);
        $b = array_fill_keys($B,1);

        $min = min(count(array_diff_key($a,$b)),count(array_diff_key($b, $a)));
        $max = max(count(array_diff_key($a,$b)),count(array_diff_key($b, $a)));

        $intersect = count(array_intersect_key($a,$b));

        return $intersect/($intersect + ($beta * ($alpha * $min + $max*(1-$alpha)) ));
    }

}
