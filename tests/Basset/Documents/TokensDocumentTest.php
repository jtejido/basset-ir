<?php

namespace Basset\Documents;


class TokensDocumentTest extends BaseDocuments
{


    /**
     * @dataProvider provideTokens
     */
    public function testDocuments($tokens)
    {
        $document = new TokensDocument($tokens);

        $this->assertTrue(
            $this->similar($tokens,$document->getDocument())
        );
    }

}
