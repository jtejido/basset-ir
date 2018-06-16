<?php


namespace Basset\Feature;

use Basset\Documents\DocumentInterface;
use Basset\Index\IndexReader;
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
     * @param  IndexReader $indexReader
     * @param  WeightedModelInterface $model OPTIONAL defaults to null|TermCount
     */
	public function __construct(IndexReader $indexReader, WeightedModelInterface $model, FeatureVector $doc)
    {
        $this->indexReader = $indexReader;
        $this->indexSearch = new IndexSearch($this->indexReader);
        $this->model = $model;
        $this->doc = $doc;

        if($this->model === null) {
    		$this->model = new TermCount;
    	}
    }

    /**
     * Returns the feature with transformed weights.
     *
     * @return array
     */
    public function getFeature(): array
    {

		$tokenSum = array_sum($this->doc->getFeature());

		$tokenCount = count($this->doc->getFeature());

    	$function = function ($key, $feature) use($tokenSum, $tokenCount) {

						if($stats = $this->indexSearch->search($key)) {
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