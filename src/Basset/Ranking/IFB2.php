<?php

namespace Basset\Ranking;



/**
 * IFB2 is a DFR class for ranking documents against a query based on Inverse Term Frequency model for
 * randomness, the ratio of two Bernoulli’s processes for first normalisation, and Normalisation 2 for term 
 * frequency normalisation .
 *
 * The implementation is based on G. Amati's paper:
 * http://theses.gla.ac.uk/1570/1/2003amatiphd.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class IFB2 extends SimilarityBase implements ScoringInterface
{

    const C = 1.0;

    protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c    = 1.0;

    }

    /**
     * Returns tf Normalization 2.
     * https://en.wikipedia.org/wiki/Divergence-from-randomness_model#Term_Frequency_Normalization
     *
     * The parameter c can be set automatically, as described by He and Ounis 'Term Frequency Normalisation
     * Tuning for BM25 and DFR model', in Proceedings of ECIR'05, 2005
     * @param  int $length
     * @param  int $avg_dl
     * @return float
     */
    private function getTfN2($docLength, $avg_dl)
    {
        return $this->math->DFRlog(1 + ($this->c * $avg_dl)/$docLength);
    }

    /**
     * @param  int $tf
     * @param  int $docLength
     * @param  int $docUniqueLength
     * @param  int $keyFrequency
     * @param  int $keylength
     * @return float
     */
    public function score($tf, $docLength, $docUniqueLength, $keyFrequency, $keylength)
    {

        $score = 0;

        if($tf > 0){
            $TF = $tf * $this->getTfN2($docLength, $this->getAverageDocumentLength());
            $NORM = ($this->getTermFrequency() + 1) / ($this->getDocumentFrequency() * ($TF + 1));
            $score += ($TF * $this->math->DFRlog(($this->getTermFrequency()+1)/0.5) * $keyFrequency * $NORM);
        }

        return $score;

    }


}