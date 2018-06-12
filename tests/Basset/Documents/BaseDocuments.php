<?php

namespace Basset\Documents;

use \PHPUnit_Framework_TestCase;

class BaseDocuments extends PHPUnit_Framework_TestCase
{
    public function provideTokens()
    {
        return array(
            array(array("Lorem", "ipsum", "dolor", "sit", "amet,", "consectetur", "adipiscing", "elit."))
        );
    }

    protected function similar($a, $b) {
	  if (count(array_diff_assoc($a, $b))) {
	    return false;
	  }

	  foreach($a as $k => $v) {
	    if ($v !== $b[$k]) {
	      return false;
	    }
	  }
	  return true;
	}

}
