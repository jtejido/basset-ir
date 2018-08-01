<?php


namespace Basset\Documents;

use Basset\Utils\TransformationInterface;

/**
 * Represents a bag-of-words(BOW) object.
 * TokensDocument represents the most basic form for all text documents being added in and processed.
 *
 * @var $tokens
 *
 * @example new TokensDocument(array('how', 'do', 'you', 'do?'));
 *
 * @author Jericko Tejido <jtbibliomania@gmail.com>
 */
class TokensDocument
{
    
    protected $tokens;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Simply return the tokens received in the constructor
     *
     * @return array
     */
    public function getDocument(): array
    {
        return $this->tokens;
    }

    /**
     * Apply the transform to each token. Filter out the null tokens.
     *
     * @param TransformationInterface $transform
     */
    public function applyTransformation(TransformationInterface $transform)
    {
        foreach($this->tokens as $k => &$v) {
            $w = $transform->transform($v);
            if(!empty($w)) {
                $v = $w;
            }
        }
    }
}
