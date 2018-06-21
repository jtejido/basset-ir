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
 * So UMass at TREC 2004 maintained the information in the original query model by linearly interpolating the relevance model with the query model.
 * Lambda has the same value as found in JelinekMercerLM.
 *
 * @see http://homepages.inf.ed.ac.uk/vlavrenk/doc/rm.pdf
 * @see https://trec.nist.gov/pubs/trec13/papers/umass.novelty.hard.pdf
 *
 * @var $lambda
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class RelevanceModel extends Feedback implements PRFInterface
{

    CONST LAMBDA = 0.7;

    private $lambda;

    /**
     * @param int $feedbackdocs
     * @param int $feedbackterms
     */

    public function __construct(int $feedbackdocs = parent::TOP_REL_DOCS, int $feedbackterms = parent::TOP_REL_TERMS, float $lambda = self::LAMBDA)
    {
        parent::__construct($feedbackdocs, $feedbackterms);
    }


    /**
     * Expands original query based on array of relevant docs received.
     *
     * @param  array $docIds
     * @return array
     */
    protected function queryExpand(array $docIds): FeatureInterface
    {

        

        $queryVector = $this->transformVector($this->getModel()->getQueryModel(), $this->getQuery())->getFeature();

        $vocab = array();

        $fbDocVectors = array();

        foreach($docIds as $class => $score) {
            $doc = $this->indexsearch->getDocumentVector($class);
            $docVector = $this->transformVector($this->getModel()->getQueryModel(), $doc)->getFeature();
            $vocab = array_merge($vocab, $docVector);
            $fbDocVectors[$class] = $docVector;
        }

        $relevantVector = new FeatureVector;

        foreach($vocab as $term => $weight) {

            $fbWeight = 0;

            $totalCount = 0;

            foreach($fbDocVectors as $class => $vector) {
                    $totalCount += count($vector);
                    if(isset($vector[$term])) {    
                        $docProb = $vector[$term] / array_sum($vector);
                        $docProb *= exp($docIds[$class]);
                        $fbWeight += $docProb;
                    }
            }

                $fbWeight /= $totalCount;
                $fbWeight = isset($queryVector[$term]) ? (1-$this->lambda) * $queryVector[$term] + ($this->lambda * $fbWeight) : $fbWeight;
                $relevantVector->addTerm($term, $fbWeight);

        }

        

        

        $relevantVector->snip($this->feedbackterms);

        // we just need the top N of new query
        return $relevantVector;

    }


    
}
