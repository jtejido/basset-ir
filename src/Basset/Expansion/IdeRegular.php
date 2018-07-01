<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureInterface;
use Basset\Feature\FeatureVector;

/**
 * This is Ide's Regular algorithm.
 * 
 * @see http://sigir.org/files/museum/pub-09/VIII-1.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class IdeRegular extends Feedback implements PRFVSMInterface
{

    CONST ALPHA = 1;

    CONST BETA = 0.75;

    CONST GAMMA = 0.25;

    private $alpha;

    private $beta;

    private $gamma;

    /**
     * @param int $feedbackdocs
     * @param int $feedbacknonreldocs
     * @param int $feedbackterms
     * @param float $alpha
     * @param float $beta
     * @param float $gamma
     */

    public function __construct(int $feedbackdocs = self::TOP_REL_DOCS, int $feedbacknonreldocs = self::TOP_NON_REL_DOCS, int $feedbackterms = self::TOP_REL_TERMS, float $alpha = self::ALPHA, float $beta = self::BETA, float $gamma = self::GAMMA)
    {
        parent::__construct($feedbackdocs, $feedbacknonreldocs, $feedbackterms);
        $this->alpha = $alpha;
        $this->beta = $beta;
        $this->gamma = $gamma;
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

        array_walk_recursive($queryVector, function (&$item, $key) 
                {
                    $item *= $this->alpha;
                }
            );

        $relevantVector->addTerms($queryVector);

        foreach($this->getRelevantDocuments() as $value) {
            $doc = $this->getIndexManager()->getDocumentVector($value->getId());
            $relevantDocVector = $this->transformVector($this->getModel(), $doc)->getFeature();
            arsort($relevantDocVector);
            array_splice($relevantDocVector, $termCount);
            array_walk_recursive($relevantDocVector, function (&$item, $key) 
                {
                    $item *= $this->beta;
                }
            );
            $relevantVector->addTerms($relevantDocVector);
        }

        foreach($this->getNonRelevantDocuments() as $value) {
            $doc = $this->getIndexManager()->getDocumentVector($value->getId());
            $nonRelevantDocVector = $this->transformVector($this->getModel(), $doc)->getFeature();
            arsort($nonRelevantDocVector);
            array_splice($nonRelevantDocVector, $termCount);
            array_walk_recursive($nonRelevantDocVector, function (&$item, $key) 
                {
                    $item *= $this->gamma * -1;
                }
            );
            $relevantVector->addTerms($nonRelevantDocVector);
        }

        return $relevantVector;

    }


    
}
