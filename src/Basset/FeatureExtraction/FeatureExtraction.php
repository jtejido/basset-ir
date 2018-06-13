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

	public function __construct(IndexReader $indexReader)
    {
        $this->indexReader = $indexReader;
        $this->indexSearch = new IndexSearch($this->indexReader);
    }

    /**
     * @param  DocumentInterface $doc
     * @return array
     */
    public function getFeature(array $doc, WeightedModelInterface $model = null): array
    {

    	if($model === null) {
    		$model = new TermCount;
    	}

		$tokenSum = array_sum($doc);

		$tokenCount = count($doc);

    	$function = function ($key, $feature) use ($tokenSum, $tokenCount, $model) {

						if($stats = $this->indexSearch->search($key)) {
			    			$model->setStats($stats);
			    		}

			        	return array($key => $model->getScore($feature, $tokenSum, $tokenCount));

			         };

        return $this->array_map_assoc($function, $doc);
    }

    // A solution for returning keys associated with the weighted values
    private function array_map_assoc(callable $function, array $doc): array
    {
	    return array_merge(...array_map($function, array_keys($doc), $doc));
	}

}