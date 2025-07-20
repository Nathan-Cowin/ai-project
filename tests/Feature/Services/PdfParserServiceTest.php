<?php

use App\Services\PdfParserService;
use Mockery\MockInterface;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser;

use function Pest\Laravel\partialMock;

/** extractText() */
it('extracts text from a PDF file successfully',
    /** @throws Exception */
    function () {
    $filePath = 'path/to/pdf';
    $expectedText = 'This is some extracted text from the PDF.';

    /** @var MockInterface|Parser $parser */
    $parser = partialMock(Parser::class);

    /** @var MockInterface|Document $document */
    $document = partialMock(Document::class);
    $document->shouldReceive('getText')->andReturn($expectedText);

    $parser->shouldReceive('parseFile')->with($filePath)->andReturn($document);

    $result = new PdfParserService($parser)->extractText($filePath);

    expect($result)->toBe($expectedText);
});

/** extractText() throws */
it('throws an exception if parsing the PDF fails',
    /** @throws Exception */
    function () {
    $filePath = 'invalid/path/to/pdf';

    /** @var MockInterface|Parser $parser */
    $parser = partialMock(Parser::class);

    $parser->shouldReceive('parseFile')->with($filePath)->andThrow('File not found');

    new PdfParserService($parser)->extractText($filePath);

})->throws('File not found');
