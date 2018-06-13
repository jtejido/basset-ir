<?php


namespace Basset\Documents;

use Basset\Utils\TransformationInterface;

/**
 * An object that wraps the Tokenized Document. It accepts $class as optional mainly for labeling purposes, 
 * otherwise it's null.
 * 
 * @see TokensDocument
 *
 * @var $d
 * @var $class
 *
 * @example new Document(new TokensDocument(array('how', 'do', 'you', 'do?')));
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class Document implements DocumentInterface
{

    private $d;

    private $class;

    /**
     * @param string $class
     * @param TokensDocument $d
     */
    public function __construct(TokensDocument $d, string $class = null)
    {
        $this->d = $d;
        $this->class = $class;       
    }

    /**
     * Returns the Tokenized Document as received by TokensDocument
     *  
     * @return array
     */
    public function getDocument(): array
    {
        return $this->d->getDocument();
    }

    /**
     * Returns the assigned label for the given TokensDocument
     *  
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * Apply the transform to the document
     *
     * @param TransformationInterface $transform
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        $this->d->applyTransformation($transform);
    }
}
