<?php

namespace Basset\Stemmers;


interface Basset extends Stemmer
{

    public function stem($word);

}
