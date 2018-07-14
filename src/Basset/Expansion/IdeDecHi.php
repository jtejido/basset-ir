<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureVector;

/**
 * This is Ide's Dec Hi algorithm, where it re-weighs term based that includes the top-most non-relevant documents in the 
 * computation.
 * 
 * @see http://sigir.org/files/museum/pub-09/VIII-1.pdf
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class IdeDecHi extends Feedback implements PRFVSMInterface
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
     * @param  FeatureVector $queryVector The query to be expanded
     * @return FeatureVector
     */
    public function expand(FeatureVector $queryVector): FeatureVector
    {

        $relevantVector = new FeatureVector;

        $queryVector = $this->transformVector($this->getModel(), $queryVector)->getFeature();

        $termCount = count($queryVector) + $this->feedbackterms;

        array_walk_recursive($queryVector, function (&$item, $key) 
            {
                $item *= $this->alpha;
            }
        );

        $relevantVector->addTerms($queryVector);

        $lastResult = $this->getTopNonRelevantDocuments();

        $lastdoc = $this->getIndexManager()->getDocumentVector($lastResult->getId());

        $lastDocVector = $this->transformVector($this->getModel(), $lastdoc)->getFeature(); 

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

        array_walk_recursive($lastDocVector, function (&$item, $key)
                {
                    $item *= $this->gamma * -1;
                }
            );
        arsort($lastDocVector);
        array_splice($lastDocVector, $termCount);
        $relevantVector->addTerms($lastDocVector);

        return $relevantVector;

    }


    
}
