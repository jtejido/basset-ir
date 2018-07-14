<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureVector;
use Basset\Metric\CosineSimilarity;

/**
 * This is the Differential Evolution approach to Query Expansion. This is a personal experiment for an EA-based relevance
 * feedback.
 *
 * DE optimizes a problem by maintaining a population of candidate solutions and creating new candidate solutions by 
 * combining existing ones according to its simple formulae, and then keeping whichever candidate solution has the best
 * score or fitness on the optimization problem at hand.
 *
 * Experimental stage
 *
 * @link https://en.wikipedia.org/wiki/Differential_evolution
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class DifferentialEvolution extends Feedback implements PRFVSMInterface
{


    const F = 0.7;

    const CR = 0.9;

    /**
     * @param int $feedbackdocs
     * @param int $feedbacknonreldocs
     * @param int $feedbackterms
     * @param int $crossoverProb
     * @param int $differentialWeight
     */

    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS)
    {
        parent::__construct($feedbackdocs, $feedbacknonreldocs, $feedbackterms);
    }

    /**
     * Expands original query based on array of relevant docs received.
     *
     * @param  FeatureVector $queryVector The query to be expanded
     * @return FeatureVector
     */
    public function expand(FeatureVector $queryVector): FeatureVector
    {

        $relevantVector = new FeatureVector;

        $queryVector = $this->transformVector($this->getModel(), $queryVector)->getFeature();

        $termCount = $this->feedbackterms;

        $vocab = array();


        foreach($this->getRelevantDocuments() as $value) {
            $doc = $this->getIndexManager()->getDocumentVector($value->getId());
            $relevantDocVector = $this->transformVector($this->getModel(), $doc)->getFeature();
            $docs[$value->getId()] = $relevantDocVector;
            $vocab = array_merge($vocab, $relevantDocVector);
        }
        ksort($vocab);

        $ctr = 0;
        foreach($docs as $id => $doc) {

            foreach($vocab as $key => $value) {
                if(isset($doc[$key])) {
                    $newDocs[$ctr][$key] = $doc[$key];
                } else {
                    $newDocs[$ctr][$key] = 0;
                }
            }

            $ctr++;

        }

        $generation = 1;

        $bestDocs = array();

        $bestDocs = array_merge($bestDocs, $newDocs); // keep the candidate solutions

        while($generation <= 100) {
            for($i = 0; $i < count($newDocs); $i++) {
                do {
                    $a = array_rand($newDocs);
                } while ($a == $i);

                do {
                    $b = array_rand($newDocs);
                } while ($b == $i || $b == $a);

                do {
                    $c = array_rand($newDocs);
                } while ($c == $i || $c == $a || $c == $b);

                $trial = array();

                $j = array_rand($newDocs[$i]);

                for ($k = 0; $k < count($vocab); $k++) {

                    if ($this->frand(0, 1) < self::CR || $k == (count($vocab) - 1)) {
                        $trial[$j] = $newDocs[$c][$j] + self::F * ($newDocs[$a][$j] - $newDocs[$b][$j]);
                    } else {
                        $trial[$j] = $newDocs[$i][$j];
                    }

                    $j = array_rand($newDocs[$i]);
                }

                $trialFitness = $this->fitnessFunction($trial, $queryVector);
                $origFitness = $this->fitnessFunction($newDocs[$i], $queryVector);

                if ($trialFitness >= $origFitness) {
                    foreach($vocab as $term => $value) {
                        if(isset($trial[$term])) {
                            $candidateDocs[$i][$term] = $trial[$term];
                        } else {
                            $candidateDocs[$i][$term] = $value;
                        }
                        
                    }
                } else {
                    $candidateDocs[$i] = $newDocs[$i];
                }
            }
            
            $bestDocs[] = $candidateDocs[$this->getFittest($candidateDocs, $queryVector)['key']]; // add the best with the solutions

            $generation++;

        }

        foreach($bestDocs as $doc) {

            $newDoc = array();

            foreach($doc as $term => $value) {
                $newDoc[$term] = $value;
            }
            arsort($newDoc);
            array_splice($newDoc, $termCount);
            $relevantVector->addTerms($newDoc);
        }

        return $relevantVector;

    }

    private function frand($min, $max) {
        return $min + lcg_value() * (abs($max - $min));
    }

    private function getFittest($tempPop, $query)
    {
        $score = array();

        foreach($tempPop as $id => $doc) {
            $score[$id] = $this->fitnessFunction($doc, $query);
        }

        arsort($score);
        reset($score);
        return array('key' => key($score), 'score' => $score[key($score)]);
    }

    private function fitnessFunction(array $a, array $b)
    {
        $cos = new CosineSimilarity;
        
        return $cos->similarity($a, $b);
    }

    
}
