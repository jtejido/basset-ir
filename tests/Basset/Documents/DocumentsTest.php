<?php

namespace Basset\Documents;


class DocumentsTest extends BaseDocuments
{   

	/**
     * @dataProvider provideTokens
     */
    public function testDocuments($tokens)
    {
        $document = new Document(new TokensDocument($tokens));

        $this->assertTrue(
            $this->similar($tokens,$document->getDocument())
        );
    }

}
