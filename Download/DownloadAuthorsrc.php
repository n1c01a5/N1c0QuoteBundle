<?php

namespace N1c0\QuoteBundle\Download;

use Pandoc\Pandoc;

class DownloadAuthorsrc
{
    private $appAuthorsrc;

    public function __construct($appAuthorsrc)
    {
        $this->appAuthorsrc = $appAuthorsrc;
    }

    public function getConvert($id, $format)
    {
        $pandoc = new Pandoc();

        $authorsrc = $this->appAuthorsrc->findAuthorsrcById($id);

        $author = $authorsrc->getName() . ' ' . $authorsrc->getFirstName();
        $date   = $authorsrc->getBirthday()->format("m M Y");

        $raw = "Title: Fiche auteur\nAuthor: $author\nDate: $date\n\n";
        $raw .= $authorsrc->getBio();

        $options = array(
            "latex-engine" => "xelatex",
            "from"         => "markdown_mmd",
            "to"           => $format,
        );

        return $pandoc->runWith($raw, $options);
    }
}
