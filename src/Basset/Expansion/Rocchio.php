<?php


namespace Basset\Expansion;

use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Feature\FeatureInterface;
use Basset\Feature\FeatureVector;

/**
 * This is Rocchio's Algorithm for expanding terms based on feedback documents received. As we will not want for non-relevant docs to actually be
 * computed, we'll omit it from the equation.
 * 
 * @see TokensDocument
 *
 * @var $beta
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class Rocchio extends Feedback implements PRFInterface
{

    CONST BETA = 0.75;

    private $beta;

    /**
     * @param int $feedbackdocs
     * @param int $feedbackterms
     */

    public function __construct(int $feedbackdocs = parent::TOP_REL_DOCS, int $feedbackterms = parent::TOP_REL_TERMS, float $beta = self::BETA)
    {
        parent::__construct($feedbackdocs, $feedbackterms);
        $this->beta = $beta;
    }


    /**
     * Expands original query based on array of relevant docs received.
     *
     * @param  array $docIds
     * @return array
     */
    protected function queryExpand(array $docIds): FeatureInterface
    {

        $relevantVector = new FeatureVector;

        // re-weight the query to match that of relevant docs
        $queryVector = $this->transformVector($this->getModel(), $this->getQuery())->getFeature(); 

        /**
         * Rocchio's algorithm reduces the weight from the docs' terms. default BETA is 0.75.
         */
        foreach($docIds as $class => $score) {
            $doc = $this->indexsearch->getDocumentVector($class);
            $docVector = $this->transformVector($this->getModel(), $doc)->getFeature(); 
            array_walk_recursive($docVector, function (&$item, $key) 
                {
                    $item *= $this->beta / $this->feedbackdocs;
                }
            );
            $relevantVector->addTerms($docVector);
        }

        // combine the new terms with the original query
        $relevantVector->addTerms($queryVector);
        $relevantVector->snip($this->feedbackterms);

        // we just need the top N of new query
        return $relevantVector;

    }


    
}
