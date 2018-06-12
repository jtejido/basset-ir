<?php

declare(strict_types=1);

namespace Basset\Tokenizers;

/**
 * Simple white space tokenizer.
 * Break on every white space
 */
class WhitespaceTokenizer implements TokenizerInterface
{
    const PATTERN = '/[\pZ\pC]+/u';

    public function tokenize(string $str): array
    {
        $arr = array();

        return preg_split(self::PATTERN,$str,-1,PREG_SPLIT_NO_EMPTY);
    }
}
