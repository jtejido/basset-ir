<?php
namespace Basset\Tokenizers;


class WhitespaceTokenizerTest extends BaseTokenizers
{

    /**
     * @dataProvider provideSentence
     */
    public function testTokenizerOnAscii($str)
    {
        $tok = new WhitespaceTokenizer();

        $tokens = array('This','is','a','simple','sentence','with,','a',
        'lot','of','space.','and','some','o.ther','stuff');
        $this->assertEquals(
            $tokens,
            $tok->tokenize($str)
        );
    }

}