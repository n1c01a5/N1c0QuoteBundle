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

        $raw = '%'.$quote->getTitle(); 
        $raw .= "\r\n";
        $raw .= '%'; 

        foreach($quote->getAuthorsrcs() as $author) {
            $raw .= $author.' ;';
        }
        $raw .= "\r\n";
        $raw .= '%'.$quote->getDate()->format("m M Y");  
        $raw .= "\r\n";
        $raw .= $quote->getBody();

        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown",
            "to"           => $format,
        );

        return $pandoc->runWith($raw, $options);
    }
}
