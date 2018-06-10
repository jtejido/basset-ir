<?php

declare(strict_types=1);

namespace Basset\Structure;


interface TrieManagerInterface
{

    public function addEntry(string $key, $value = null);

    public function search($prefix);

    public function getData();

    public function getTrie();


}
