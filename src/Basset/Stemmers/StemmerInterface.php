<?php

namespace Basset\Stemmers;


interface StemmerInterface
{
    public function stem($word);
}
