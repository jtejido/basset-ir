<?php


namespace Basset\FeatureExtraction;

use Basset\Documents\DocumentInterface;
use Basset\Index\IndexReader;
use Basset\Index\IndexSearch;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Models\TermCount;

/**
 * A class that does feature extraction given an Index(for term stats), array of docs and a model to pull the 
 * features from.
 * @see https://en.wikipedia.org/wiki/Feature_(machine_learning)
 * 
 * @see DocumentInterface
 *
 * @example
 * $fe = new FeatureExtraction();
 * $fe->getFeature($doc);
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class FeatureExtraction implements FeatureExtractionInterface
{

	/**
     * @param  IndexReader $indexReader
     * @param  WeightedModelInterface $model OPTIONAL defaults to null|TermCount
     */
	public function __construct(IndexReader $indexReader, WeightedModelInterface $model = null)
    {
        $this->indexReader = $indexReader;
        $this->indexSearch = new IndexSearch($this->indexReader);
        $this->model = $model;

        if($this->model === null) {
    		$this->model = new TermCount;
    	}
    }

    /**
     * @param  array $doc
     * @return array
     */
    public function getFeature(array $doc): array
    {

		$tokenSum = array_sum($doc);

		$tokenCount = count($doc);

    	$function = function ($key, $feature) use($tokenSum, $tokenCount) {

						if($stats = $this->indexSearch->search($key)) {
			    			$this->model->setStats($stats);
			    			$feature = $this->model->getScore($feature, $tokenSum, $tokenCount);
			    		} else {
			    			$feature = 0;
			    		}

			    		return array($key => $feature);

			         };

        return $this->array_map_assoc($function, $doc);
    }

    // A solution for returning keys associated with the weighted values
    private function array_map_assoc(callable $function, array $doc): array
    {
	    return array_merge(...array_map($function, array_keys($doc), $doc));
	}


}