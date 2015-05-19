<?php

namespace N1c0\QuoteBundle\EventListener;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\BookEvent;
use N1c0\QuoteBundle\Markup\ParserInterface;
use N1c0\QuoteBundle\Model\RawBookInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a book for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class BookMarkupListener implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \N1c0\QuoteBundle\Markup\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses raw book data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\QuoteBundle\Event\BookEvent $event
     */
    public function markup(BookEvent $event)
    {
        $book = $event->getBook();

        if (!$book instanceof RawBookInterface) {
            return;
        }

        $result = $this->parser->parse($book->getBody());
        $book->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::book_PRE_PERSIST => 'markup');
    }
}
