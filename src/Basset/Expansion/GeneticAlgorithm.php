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
    
    CONST MUTATION_RATE = 0.7;

    /**
     * @param int $feedbackdocs
     * @param int $feedbacknonreldocs
     * @param int $feedbackterms
     */

    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS)
    {
        parent::__construct($feedbackdocs, $feedbacknonreldocs, $feedbackterms);
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

        $termCount = count($queryVector) + $this->feedbackterms;

        $relevantVector->addTerms($queryVector);

        $vocab = array();


        foreach($this->getRelevantDocuments() as $value) {
            $doc = $this->getIndexManager()->getDocumentVector($value->getId());
            $relevantDocVector = $this->transformVector($this->getModel(), $doc)->getFeature();
            $docs[$value->getId()] = $relevantDocVector;
            $vocab = array_merge($vocab, $relevantDocVector);
        }
        ksort($vocab);

        foreach($docs as $id => $doc) {

            foreach($vocab as $key => $value) {
                if(isset($doc[$key])) {
                    $newDocs[$id][$key] = 1;
                } else {
                    $newDocs[$id][$key] = 0;
                }
            }

        }

        for($i = 0; $i <= 100; $i++) {
            foreach($newDocs as $id => &$doc) {
                $indiv1 = $this->poolSelection($newDocs);
                $indiv2 = $this->poolSelection($newDocs);
                $newInd = $this->crossover($indiv1, $indiv2);
                $doc = $this->mutate($newInd);
            }
        }

        foreach($newDocs as $id => $doc) {
            $newDoc = array();

            foreach($doc as $term => $value) {
                if(isset($vocab[$term]) && $value == 1) {
                    $newDoc[$term] = $value;
                }
            }
            arsort($newDoc);
            array_splice($newDoc, $termCount);

            $relevantVector->addTerms($newDoc);
        }

        return $relevantVector;

    }

    private function random() {
      return (float)rand()/(float)getrandmax();
    }

    private function crossover($indiv1, $indiv2) 
    {
       $newGene = array();

        foreach($indiv1 as $key => $value) {
            if ($this->random() <= self::UNIFORM_RATE)
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
            if ($this->random() <= self::MUTATION_RATE) {
                $gene = rand(0,1);
                $value = $gene;
            }
        }

        return $indiv;
    }

    private function poolSelection($pop)
    {
  
        foreach($pop as $id => $doc) {
            $randomId = array_rand($pop);
            $tempPop[$id] = $pop[$randomId];
        }

        $fittest = $this->getFittest($tempPop, $pop);
        return $fittest;
    }

    private function getFittest($tempPop, $pop)
    {
        $score = array();

        foreach($tempPop as $id => $doc) {
            $score[$id] = $this->score($doc, $pop[$id]);
        }

        asort($score);
        reset($score);

        return $tempPop[key($score)];
    }


    private function score(array $a, array $b)
    {
        $cos = new CosineSimilarity;
        
        return $cos->similarity($a, $b);
    }


    
}
