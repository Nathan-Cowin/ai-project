<?php

namespace App\Services;

use Exception;
use Smalot\PdfParser\Parser;

readonly class PdfParserService
{
    public function __construct(
        private Parser $parser
    ){}

    /**
     * @throws Exception
     */
    public function extractText($filePath): string
    {
        return $this->parser->parseFile($filePath)->getText();
    }
}
