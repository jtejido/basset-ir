<?php

namespace Basset\Stemmers;

use Basset\Utils\TransformationInterface;

/**
 * This stemmer removes affixes according to a regular expression.
 */
class RegexStemmer extends Stemmer implements TransformationInterface, StemmerInterface
{

    protected $regex;
    protected $min;

    /**
     * @param string  $regexstr The regex that will be passed to preg_replace
     * @param integer $min      Do nothing for tokens smaller than $min length
     */
    public function __construct($regexstr,$min=0)
    {
        $this->regex = $regexstr;
        $this->min = $min;
    }

    public function stem($w)
    {
        if (mb_strlen($w,'utf-8')>=$this->min)
            return preg_replace($this->regex,'',$w);
        return $w;
    }

    /**
     * Apply transformation.
     */
    public function transform($w)
    {
        return $this->stem($w);
    }

}
