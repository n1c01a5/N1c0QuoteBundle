<?php

namespace N1c0\QuoteBundle\Event;

use N1c0\QuoteBundle\Model\BookInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a book.
 */
class BookEvent extends Event
{
    private $book;

    /**
     * Constructs an event.
     *
     * @param \n1c0\QuoteBundle\Model\BookInterface $book
     */
    public function __construct(BookInterface $book)
    {
        $this->book = $book;
    }

    /**
     * Returns the book for this event.
     *
     * @return \n1c0\QuoteBundle\Model\BookInterface
     */
    public function getBook()
    {
        return $this->book;
    }
}
