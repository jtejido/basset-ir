<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureInterface;
use Basset\Feature\FeatureVector;

/**
 * This Class implements a hybrid Lavrenko and Croft’s Relevance Model. A variation of pseudo-relevance feedback methods developed for the language
 * modeling framework. While the two methods given on their paper works, it fails to give more weights to the terms present in the actual query
 * itself, making it problematic.
 *
 * So UMass at TREC 2004 HARD track maintained the information in the original query model by linearly interpolating the relevance model with the query model.
 * Lambda has the same value as found in JelinekMercerLM.
 *
 * @see http://homepages.inf.ed.ac.uk/vlavrenk/doc/rm.pdf
 * @see https://trec.nist.gov/pubs/trec13/papers/umass.novelty.hard.pdf
 *
 * @var $lambda
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class RelevanceModel extends Feedback implements PRFVSMInterface
{

    CONST LAMBDA = 0.7;

    private $lambda;

    /**
     * @param int $feedbackdocs
     * @param int $feedbacknonreldocs
     * @param int $feedbackterms
     * @param float $lambda
     */

    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS, float $lambda = self::LAMBDA)
    {
        parent::__construct($feedbackdocs, $feedbacknonreldocs, $feedbackterms);
        $this->lambda = $lambda;
    }

    /**
     * Expands original query based on array of relevant docs received.
     *
     * @param  FeatureInterface $queryVector The query to be expanded
     * @return FeatureInterface
     */
    public function expand(FeatureInterface $queryVector): FeatureInterface
    {

        $vocab = array();

        $fbDocVectors = array();

        $queryVector = $queryVector->getFeature();

        $termCount = count($queryVector) + $this->feedbackterms;

        foreach($this->getRelevantDocuments() as $value) {
            $doc = $this->getIndexManager()->getDocumentVector($value->getId());
            $docVector = $this->transformVector($this->getModel(), $doc)->getFeature();
            arsort($docVector);
            array_splice($docVector, $termCount);
            $vocab = array_merge($vocab, $docVector);
            $fbDocVectors[$value->getId()] = $docVector;
            $rsvs[$value->getId()] = $value->getScore();
        }

        $relevantVector = new FeatureVector;

        foreach($vocab as $term => $weight) {

            $fbWeight = 0;

            $totalCount = 0;

            foreach($fbDocVectors as $id => $vector) {
                    if(isset($vector[$term])) {
                        $totalCount += count($vector);
                        $docProb = $vector[$term] / array_sum($vector);
                        $docProb *= exp($rsvs[$id]);
                        $fbWeight += $docProb;
                    }
            }

            if($fbWeight > 0) {
                $fbWeight /= $totalCount;
                $fbWeight = isset($queryVector[$term]) ? (1-$this->lambda) * $queryVector[$term] + ($this->lambda * $fbWeight) : $fbWeight;
                $relevantVector->addTerm($term, $fbWeight);
            }

        }

        return $relevantVector;

    }


    
}
