<?php

namespace Basset\Stemmers;


abstract class Stemmer
{


    abstract public function stem($word);

    /**
     * Apply the stemmer to every single token.
     *
     * @return array
     */
    public function stemAll(array $tokens)
    {
        return array_map(array($this,'stem'),$tokens);
    }

}
