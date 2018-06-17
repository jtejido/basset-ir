<?php

namespace Basset\Feature;

/**
 * FeatureVector is a class for wrapping an array of token features.
 * @see https://en.wikipedia.org/wiki/Feature_(machine_learning)
 *
 * @example
 * $vector = new FeatureVector(['a' => 1.2, 'was' => 2.4])
 * $vector->getFeature();
 * $vector->addTerm('here', 2.1);
 * $vector->clip(2); // returns first N of feature vector in ascending order. e.g. 'I was'
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class FeatureVector implements FeatureInterface
{

    /**
     * The array of term => weight features.
     *
     * @param array $features
     */
	public function __construct(array $features = array())
    {
        $this->features = $features;
    }

    /**
     * Add/Update term's weighted value.
     *
     * @param string $term
     * @param float $weight
     */
    public function addTerm(string $term, float $weight)
    {
        $this->features[$term] = $weight;
    }

    /**
     * Merges existing features with new array.
     *
     * @param array $terms
     */
    public function addTerms(array $terms)
    {
		$this->features = array_merge($this->features, $terms);
    }

    /**
     * Merges existing features with new array.
     *
     * @param array $terms
     */
    public function removeTerm(string $terms)
    {
        if (isset($this->features[$terms])){
            unset($this->features[$terms]);
        }
    }

    /**
     * Returns the array.
     *
     * @return array
     */
    public function getFeature(): array
    {
        return $this->features;
    }

    /**
     * Orders the array in ascending order, then clip the first N of array.
     *
     * @return array
     */
    public function clip(int $length): array
    {
        arsort($this->features);
        return array_slice($this->features, 0, $length, true);
    }

    /**
     * Orders the array in ascending order, then clip the first N of array.
     *
     * @return array
     */
    public function snip(int $length): array
    {
        arsort($this->features);
        return array_splice($this->features, $length);
    }


}