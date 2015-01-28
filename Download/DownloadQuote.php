<?php

namespace N1c0\QuoteBundle\Download;

use Pandoc\Pandoc;

class DownloadQuote 
{
    private $appQuote;

    public function __construct($appQuote)
    {
        $this->appQuote = $appQuote;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();

        $quote = $this->appQuote->findQuoteById($id);
        
        $title  = $quote->getTitle();
        $author = $quote->getAuthorsrc();
        $date   = $quote->getCreatedAt()->format("m M Y");

        $raw = "Title: $title\nAuthor: $author\nDate: $date\n\n";
        $raw .= $quote->getBody();

        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown_mmd",
            "to"           => $format,
        );

        return $pandoc->runWith($raw, $options);
    }
}
