<?php

declare(strict_types=1);

namespace Basset\Utils;

use Basset\Normalizers\English;

/**
 * In computing, stop words are words which are filtered out before or after processing of natural language data (text).
 * Though "stop words" usually refers to the most common words in a language, there is no single universal list of stop 
 * words used by all natural language processing tools, and indeed not all tools even use such a list. Some tools 
 * specifically avoid removing these stop words to support phrase search.
 *
 * This class transforms tokens. If they are listed as stop words
 * it returns null in order for the Document to remove them.
 * Otherwise it leaves them unchanged.
 */
class StopWords implements TransformationInterface
{
    protected $stopwords;
    protected $transform;

    public function __construct(array $stopwords, TransformationInterface $transform = null)
    {
        $this->stopwords = array_fill_keys(
            $stopwords,
            true
        );

        $this->transform = ($transform === null) ? new English() : $transform;
    }

    public function transform($token)
    {
        $tocheck = $token;

        if ($this->transform) {
            $tocheck = $this->transform->transform($token);
        }

        return isset($this->stopwords[$tocheck]) ? null : $token;
    }
}
