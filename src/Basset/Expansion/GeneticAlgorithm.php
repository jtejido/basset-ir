<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureVector;
use Basset\Metric\CosineSimilarity;

/**
 * This is a Genetic Algorithm approach to Query Expansion. This is a personal experiment for an EA-based relevance feedback.
 * 
 * Genetic algorithms are commonly used to generate high-quality solutions to optimization and search problems by relying 
 * on bio-inspired operators such as mutation, crossover and selection.
 *
 * Experimental stage
 *
 * @link https://en.wikipedia.org/wiki/Genetic_algorithm
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class GeneticAlgorithm extends Feedback implements PRFEAVSMInterface
{


    CONST UNIFORM_RATE = 0.8;
    
    CONST MUTATION_RATE = 0.03;

    /**
     * @param int $feedbackdocs
     * @param int $feedbacknonreldocs
     * @param int $feedbackterms
     * @param int $uniformRate
     * @param int $mutationRate
     */

    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS, $uniformRate = self::UNIFORM_RATE, $mutationRate = self::MUTATION_RATE)
    {
        parent::__construct($feedbackdocs, $feedbacknonreldocs, $feedbackterms);
        $this->uniformRate = $uniformRate;
        $this->mutationRate = $mutationRate;
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
                $newDocs[$ctr][$key] = isset($doc[$key]) ? $doc[$key] : 0;
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

            if( $generationStagnant > 100) {
                break;
            }

        }

        arsort($fittestDoc);
        array_splice($fittestDoc, $termCount);
        $relevantVector->addTerms($fittestDoc);

        return $relevantVector;

    }

    private function evolve($population, $queryVector) {

        $population[0] = $population[$this->getFittest($population, $queryVector)['key']]; // elitism
        for($i = 1; $i < count($population); $i++) {
            $indiv1 = $this->poolSelection($population, $queryVector);
            $indiv2 = $this->poolSelection($population, $queryVector);
            $newIndiv = $this->crossover($indiv1, $indiv2);
            $population[$i] = $this->mutate($newIndiv);
        }

        return $population;
    }

    private function crossover($indiv1, $indiv2) 
    {
       $newGene = array();

        foreach($indiv1 as $key => $value) {
            if ($this->math->random(0, 1) <= $this->uniformRate)
            {
                $newGene[$key] = $indiv1[$key];
            } else {
                $newGene[$key] = $indiv2[$key];
            }
        }

        return $newGene;
    }

    private function mutate($indiv) {

        foreach($indiv as $key => &$value) {
            if ($this->math->random(0, 1) <= $this->mutationRate) {
                $randKey = array_rand($indiv);
                $test = array(0 => 0, $indiv[$randKey] => 1);
                $gene = array_rand($test);
                $value = $gene;
            }
        }

        return $indiv;
    }

    private function poolSelection($docs, $query)
    {
  
        foreach($docs as $id => $doc) {
            $randomId = array_rand($docs);
            $tempPop[$id] = $docs[$randomId];
        }

        $fittest = $this->getFittest($tempPop, $query)['key'];


        return $docs[$fittest];
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
