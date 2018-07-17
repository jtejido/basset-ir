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

class DifferentialEvolution extends Feedback implements PRFEAVSMInterface
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

    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS, $crossoverProb = self::CR, $differentialWeight = self::F)
    {
        parent::__construct($feedbackdocs, $feedbacknonreldocs, $feedbackterms);
        $this->crossoverProb = $crossoverProb;
        $this->differentialWeight = $differentialWeight;
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

        $relevantVector->addTerms($queryVector);

        $termCount = count($queryVector) + $this->feedbackterms;

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

        $most_fit = 0;
        $most_fit_last = 1;
        $generation_stagnant = 0;

        while($this->getFittest($newDocs, $queryVector)['score'] > 0) {
            $most_fit = $this->getFittest($newDocs, $queryVector)['score'];
            $newDocs = $this->evolve($newDocs, $queryVector);
            if ($most_fit < $most_fit_last) {
                $most_fit_last = $most_fit;
                $generation_stagnant = 0;
            } else {
                $generation_stagnant++; // no improvement
            }

            if( $generation_stagnant > 100) {
                break;
            }
        }

        foreach($newDocs as $doc) {

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

    private function evolve($population, $queryVector) {

        $candidateDocs[0] = $population[$this->getFittest($population, $queryVector)['key']]; // elitism

        for($i = 1; $i < count($population); $i++) {
            do {
                $a = array_rand($population);
            } while ($a == $i);

            do {
                $b = array_rand($population);
            } while ($b == $i || $b == $a);

            do {
                $c = array_rand($population);
            } while ($c == $i || $c == $a || $c == $b);

            $trial = array();

            $j = array_rand($population[$i]);

            $count = count($population[$i]);

            for ($k = 0; $k < $count; $k++) {

                $j_temp = $j;


                if ($this->frand(0, 1) < $this->crossoverProb || $k == ($count - 1)) {
                    $trial[$j] = $population[$c][$j] + $this->differentialWeight * ($population[$a][$j] - $population[$b][$j]);
                } else {
                    $trial[$j] = $population[$i][$j];
                }

                do {
                    $j = array_rand($population[$i]);
                } while ($j === $j_temp);
            }

            $trialFitness = $this->fitnessFunction($trial, $queryVector);
            $origFitness = $this->fitnessFunction($population[$i], $queryVector);

            if ($trialFitness >= $origFitness) {
                foreach($population[$i] as $term => $value) {
                    if(isset($trial[$term])) {
                        $candidateDocs[$i][$term] = $trial[$term];
                    } else {
                        $candidateDocs[$i][$term] = $value;
                    }
                    
                }
            } else {
                $candidateDocs[$i] = $population[$i];
            }
        }

        return $candidateDocs;
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
