<?php

namespace Basset\Ranking;



/**
 * BB2 is a DFR class for ranking documents based on Bose-Einstein model for randomness, the ratio of 
 * two Bernoulli’s processes for first normalisation, and Normalisation 2 for term frequency normalisation
 *
 * The implementation is based on G. Amati's paper:
 * http://theses.gla.ac.uk/1570/1/2003amatiphd.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */


class BB2 extends SimilarityBase implements ScoringInterface
{

    const C = 1;


    protected $c;

    public function __construct($c = self::C)
    {
        parent::__construct();
        $this->c    = $c;
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
            $score += $NORM * $keyFrequency
                        * ( -($this->math->DFRlog($numberOfDocuments  - 1))
                            - $this->math->log2ofE()
                            + $this->math->stirlingPower(
                                $numberOfDocuments
                                    + $termFrequency
                                    - 1,
                                $numberOfDocuments
                                    + $termFrequency
                                    - $TF
                                    - 2)
                            - $this->math->stirlingPower($termFrequency, $termFrequency - $TF));
        }

        return $score;

    }

}