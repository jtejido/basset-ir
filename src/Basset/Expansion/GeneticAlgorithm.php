<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureInterface;
use Basset\Feature\FeatureVector;
use Basset\Metric\CosineSimilarity;

/**
 * This is Ide's Dec Hi algorithm, where it re-weighs term based that includes the top-most non-relevant documents in the 
 * computation.
 * 
 * @see http://sigir.org/files/museum/pub-09/VIII-1.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class GeneticAlgorithm extends Feedback implements PRFVSMInterface
{


    CONST UNIFORM_RATE = 0.8;
    
    CONST MUTATION_RATE = 0.03;

    /**
     * @param int $feedbackdocs
     * @param int $feedbacknonreldocs
     * @param int $feedbackterms
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
     * @param  FeatureInterface $queryVector The query to be expanded
     * @return FeatureInterface
     */
    public function expand(FeatureInterface $queryVector): FeatureInterface
    {

        $relevantVector = new FeatureVector;

        $queryVector = $queryVector->getFeature();

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

        $most_fit=0;
        $most_fit_last=1;
        $generation_stagnant=0;

        while($this->getFittest($newDocs, $queryVector)['score'] > 0) {
            $most_fit = $this->getFittest($newDocs, $queryVector)['score'];

            $newDocs = $this->getOffspring($newDocs, $queryVector);
            if ($most_fit < $most_fit_last) {
                $most_fit_last=$most_fit;
                $generation_stagnant = 0;
            } else {
                $generation_stagnant++; //no improvement increment may want to end early
            }

            if( $generation_stagnant > 100) {
                break;
            }

        }

        foreach($newDocs as $doc) {
            $newDoc = array();

            foreach($doc as $term => $value) {
                $newDoc[$term] = 0;
                if($value > 0) {
                    $newDoc[$term] += 1;
                }
            }
            arsort($newDoc);
            array_splice($newDoc, $termCount);
            $newVector = new FeatureVector($newDoc);
            $docVector = $this->transformVector($this->getModel(), $newVector)->getFeature();
            $relevantVector->addTerms($docVector);
        }

        return $relevantVector;

    }

    private function random() {
      return (float)rand()/(float)getrandmax();
    }

    private function getOffspring($pop, $queryVector) {

        $pop[0] = $pop[$this->getFittest($pop, $queryVector)['key']]; // elitism
        for($i = 1; $i < count($pop); $i++) {
            $indiv1 = $this->poolSelection($pop, $queryVector);
            $indiv2 = $this->poolSelection($pop, $queryVector);
            $newInd = $this->crossover($indiv1, $indiv2);
            $pop[$i] = $this->mutate($newInd);
        }

        return $pop;
    }

    private function crossover($indiv1, $indiv2) 
    {
       $newGene = array();

        foreach($indiv1 as $key => $value) {
            if ($this->random() <= $this->uniformRate)
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
            if ($this->random() <= $this->mutationRate) {
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
