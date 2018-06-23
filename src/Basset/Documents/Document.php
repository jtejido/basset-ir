<?php


namespace Basset\Documents;

use Basset\Utils\TransformationInterface;
use Basset\MetaData\MetaData;

/**
 * An object that wraps the Tokenized Document. It accepts $class as optional mainly for labeling purposes, 
 * otherwise it's null.
 * 
 * @see TokensDocument
 *
 * @var $d
 *
 * @example new Document(new TokensDocument(array('how', 'do', 'you', 'do?')));
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */

class Document implements DocumentInterface
{

    private $d;

    private $metadata;

    /**
     * @param string $class
     * @param MetaData $metadata
     */
    public function __construct(TokensDocument $d, $metadata = null)
    {
        $this->d = $d;
        $this->metadata = $metadata;
        
        if ($this->metadata === null) {
            $this->metadata = new MetaData;
        }
        
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
     * Returns the MetaData assigned for the document.
     *  
     * @return MetaData
     */
    public function getMetaData(): MetaData
    {
        return $this->metadata;
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
