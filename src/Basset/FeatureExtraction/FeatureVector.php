<?php

namespace Basset\FeatureExtraction;



class FeatureVector implements FeatureExtractionInterface
{


	public function __construct(array $features = array())
    {
        $this->features = $features;
    }

    // Add/update new weight for term.
    public function addTerm(string $term, $weight)
    {
        $this->features[$term] = $weight;
    }

    public function getFeature(): array
    {
		return $this->features;
    }

    public function clip(int $length): array
    {
        arsort($this->features);
        return array_slice($this->features, 0, $length, true);
    }


}