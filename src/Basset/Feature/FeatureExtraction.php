<?php


namespace Basset\Feature;

use Basset\Documents\DocumentInterface;
use Basset\Index\IndexManager;
use Basset\Index\IndexSearch;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Models\TermCount;

/**
 * A class that does feature extraction given an IndexReader(for term stats), FeatureVector and a model to pull the 
 * new features from.
 * @see https://en.wikipedia.org/wiki/Feature_(machine_learning)
 * 
 * @see DocumentInterface
 *
 * @example
 * $fe = new FeatureExtraction($indexReader, $model, $doc);
 * $fe->getFeature();
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class FeatureExtraction implements FeatureInterface
{

	/**
     * @param  IndexManager $indexManager
     * @param  WeightedModelInterface $model
     * @param  FeatureVector $doc The Feature to transform
     */
	public function __construct(IndexManager $indexManager, WeightedModelInterface $model, FeatureVector $doc)
    {
        $this->indexManager = $indexManager;
        $this->model = $model;
        $this->doc = $doc;
    }

    /**
     * Returns the feature with transformed weights.
     *
     * @return array
     */
    public function getFeature(): array
    {

		$tokenSum = $this->doc->getLength();

		$tokenCount = $this->doc->getCount();

    	$function = function ($key, $feature) use($tokenSum, $tokenCount) {

						if($stats = $this->indexManager->search($key)) {
			    			$this->model->setStats($stats);
			    			$feature = $this->model->getScore($feature, $tokenSum, $tokenCount);
			    		} else {
			    			$feature = 0;
			    		}

			    		return array($key => $feature);

			         };

        return $this->array_map_assoc($function);
    }

    // A solution for returning keys associated with the weighted values
    private function array_map_assoc(callable $function): array
    {
	    return array_merge(...array_map($function, array_keys($this->doc->getFeature()), $this->doc->getFeature()));
	}


}