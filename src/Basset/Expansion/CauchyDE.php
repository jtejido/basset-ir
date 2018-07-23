<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureVector;
use Basset\Metric\CosineSimilarity;

/**
 * This is the Differential Evolution approach to Query Expansion. This is a personal experiment for an EA-based relevance
 * feedback.
 *
 * This is based on Choi et. al.
 * An Adaptive Cauchy Differential Evolution Algorithm for Global Numerical Optimization.
 * doi: 10.1155/2013/969734
 *
 * This is DE/rand/1/bin framework.
 *
 * It uses individual's control parameter adapted based on the average parameter value of successfully evolved individuals'
 * parameter values by using the Cauchy distribution.
 *
 * Experimental stage
 *
 * @link https://en.wikipedia.org/wiki/Differential_evolution
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class CauchyDE extends Feedback implements PRFEAVSMInterface
{


    const F = 0.5;

    const CR = 0.9;

    const GAMMA_F = 0.1;

    const GAMMA_CR = 0.1;

    /**
     * @param int $feedbackdocs
     * @param int $feedbacknonreldocs
     * @param int $feedbackterms
     * @param int $crossoverProb
     * @param int $differentialWeight
     */

    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS, $crossoverProb = self::CR, $differentialWeight = self::F, $gammaCR = self::GAMMA_F, $gammaF = self::GAMMA_CR)
    {
        parent::__construct($feedbackdocs, $feedbacknonreldocs, $feedbackterms);
        $this->crossoverProb = $crossoverProb;
        $this->differentialWeight = $differentialWeight;
        $this->gammaCR = $gammaCR;
        $this->gammaF = $gammaF;
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

        $mostFit = 0;
        $mostFitLast = 1;
        $generationStagnant = 0;
        $mostFitCalc = $this->getFittest($newDocs, $queryVector);

        while($mostFitCalc['score'] > 0) {
            
            $mostFit = $mostFitCalc['score'];
            $newDocs = $this->evolve($newDocs, $queryVector);

            $mostFitCalc = $this->getFittest($newDocs, $queryVector);
            $fittestDoc = $newDocs[$mostFitCalc['key']];

            if ($mostFit < $mostFitLast) {
                $mostFitLast = $mostFit;
                $generationStagnant = 0;
            } else {
                $generationStagnant++; // no improvement
            }

            if( $generationStagnant > 100 || $mostFit == 1) {
                break;
            }
        }

        arsort($fittestDoc);
        array_splice($fittestDoc, $termCount);
        $relevantVector->addTerms($fittestDoc);


        return $relevantVector;

    }

    private function evolve($population, $queryVector) {


        for($i = 0; $i < count($population); $i++) {
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

                if ($this->math->random(0, 1) < $this->crossoverProb || $k == ($count - 1)) {
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

            $fMem = array();

            $crMem = array();

            if ($trialFitness >= $origFitness) {
                $fMem[] = $this->differentialWeight;
                $crMem[] = $this->crossoverProb;
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

        if($fMem !== null and $crMem !== null) {
            $fAvg = $this->math->mean($fMem);
            $crAvg = $this->math->mean($crMem);
            $df = $this->math->cauchyGenerator(0, $this->gammaF) + $fAvg;
            $cr = $this->math->cauchyGenerator(0, $this->gammaCR) + $crAvg;

            if($df < 0.1) {
                $this->differentialWeight = 0;
            } elseif ($df > 1) {
                $this->differentialWeight = 1;
            } else {
                $this->differentialWeight = $df;
            }

            if($cr < 0.1) {
                $this->crossoverProb = 0;
            } elseif ($cr > 1) {
                $this->crossoverProb = 1;
            } else {
                $this->crossoverProb = $cr;
            }

        }
        
        

        return $candidateDocs;
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
