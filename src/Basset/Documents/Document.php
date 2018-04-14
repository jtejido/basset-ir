<?php

namespace Basset\Documents;

use Basset\Utils\TransformationInterface;
use Basset\Models\Contracts\WeightedModelInterface;
use Basset\Statistics\PostingStatistics;

/**
 * A Document object that have known label/class with it.
 */
class Document implements DocumentInterface
{

    private $d;

    private $class;

    private $model;

    private $stats;

    /**
     * @param string            $class The actual label/class of the Document $d
     * @param DocumentInterface $d     The document to be processed
     */
    public function __construct(DocumentInterface $d, $class = null)
    {
        $this->d = $d;
        $this->class = $class;
        $this->model = null;
        $this->stats = new PostingStatistics($this->d);
    }

    public function getDocument()
    {
        return $this->d->getDocument();
    }

    public function getClass()
    {
        return $this->class;
    }

    /**
     * Returns the Posting Stats
     * @return PostingStatistics
     */
    public function getPostingStats()
    {
        return $this->stats;
    }

    /**
     * Returns the Posting Stats
     * @return array The tokens array
     */
    public function getTokens()
    {
        return $this->getPostingStats()->getTokens();
    }

    /**
     * Returns the term count of a given term
     * @param string $term
     * @return int
     */
    public function getTf(string $term)
    {
        return $this->getPostingStats()->getTf($term);
    }

    /**
     * Returns the term count of a given term
     * @return int
     */
    public function getDocumentLength()
    {
        return $this->getPostingStats()->getDocumentLength();
    }

    /**
     * Returns the term count of a given term
     * @return int
     */
    public function getNumberOfUniqueTerms()
    {
        return $this->getPostingStats()->getNumberOfUniqueTerms();
    }

    /**
     * Set the model for ranking
     * @param WeightedModelInterface $model
     */
    public function setModel(WeightedModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * Returns the model
     * @return WeightedModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Apply the transform to the document.
     *
     * @param TransformationInterface $transform The transformation to be applied
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        $this->d->applyTransformation($transform);
        $this->stats = new PostingStatistics($this->d);
    }
}
