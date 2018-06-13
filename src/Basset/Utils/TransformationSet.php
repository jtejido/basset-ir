<?php


namespace Basset\Utils;


/**
 * Passing different sets of transformations.
 *
 * Can be used to create, for instance, language based transformations.
 */
class TransformationSet implements TransformationInterface
{

    protected $transforms;

    /**
     * Classify the passed in variable w and then apply each transformation
     * to the output of the previous one.
     */
    public function transform($w)
    {

        foreach ($this->transforms as $t) {
            $w = $t->transform($w);
        }

        return $w;
    }

    /**
     * Register a set of transformations.
     *
     * @param string $class
     * @param array|TransformationInterface Either an array of transformations or a single transformation
     */
    public function register(array $transforms)
    {
        $this->transforms = array();
        foreach ($transforms as $t) {
            if (!($t instanceof TransformationInterface)) {
                throw new \Exception("Only instances of TransformationInterface can be registered");
            }
            $this->transforms[] = $t;
        }

    }
}
