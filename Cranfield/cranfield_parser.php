<?php

/**
 *
 * Parses XML Cranfield collection(trec_format).
 *
 */

Class CranfieldParser {

	CONST BASE = './cranfield_parsed/';

	public function __construct($file)
    {
        $this->file = $file;
    }

	public function parse()
	{
		if(!file_exists(self::BASE) && !is_dir(self::BASE)) {
            mkdir(self::BASE, 0777, true);
        }

		$doc = file_get_contents($this->file);
		$string = str_replace(array("\r", "\n"), '', $doc);
		$xml = new \SimpleXMLElement($string);
		foreach($xml as $key => $value){
			$file = null;
			$filename = null;
			foreach($value as $node => $content){
				if ($node === 'DOCNO'){
					$filename = $content;
				}

				if ($node === 'TEXT'){
					$file = $content;
				}

				if($file && $filename && !file_exists(self::BASE . $filename)) {
					file_put_contents(self::BASE . $filename, $file);
				}
				
			}
		}
	}
}

