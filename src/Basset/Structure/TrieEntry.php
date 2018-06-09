<?php

namespace Basset\Structure;


class TrieEntry
{

    public $key = null;

    public $value = null;

    public function __construct($value, $key = null) 
    {
        $this->value = $value;
        $this->key = $key;
    }

    public function setKey($key = null) 
    {
        $this->key = $key;
    }
}
