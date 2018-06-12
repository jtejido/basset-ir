<?php

namespace Basset\Tokenizers;

use \PHPUnit_Framework_TestCase;

class BaseTokenizers extends PHPUnit_Framework_TestCase
{
    public function provideSentence()
    {
        return array(
            array("This is a simple     sentence with, a    lot of space. and some o.ther     stuff")
        );
    }

}
