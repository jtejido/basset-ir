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

class FeatureVector implements FeatureInterface, \Iterator,\ArrayAccess,\Countable
{

    private $features;

    private $currentTerm;
    
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
     * Orders the array in ascending order, then get the first N of array.
     *
     * @return array
     */
    public function clip(int $length): array
    {
        arsort($this->features);
        return array_slice($this->features, 0, $length, true);
    }

    /**
     * Orders the array in ascending order, then remove items after the first N of array.
     *
     * @return array
     */
    public function snip(int $length): array
    {
        arsort($this->features);
        return array_splice($this->features, $length);
    }

    public function count() 
    {
        return count($this->features);
    }

     public function rewind()
    {
        reset($this->features);
        $this->currentTerm = current($this->features);
    }

    public function next()
    {
        $this->currentTerm = next($this->features);
    }

    public function valid()
    {
        return $this->currentTerm != false;
    }

    public function current()
    {
        return $this->currentTerm;
    }

    public function key()
    {
        return key($this->features);
    }

    public function offsetSet($key,$value)
    {
        throw new \Exception('Shouldn\'t add feature this way, add them through addTerm() or addTerms()');
    }
    public function offsetUnset($key)
    {
        throw new \Exception('Cannot unset any feature');
    }
    public function offsetGet($key)
    {
        return $this->features[$key];
    }
    public function offsetExists($key)
    {
        return isset($this->features[$key]);
    }


}