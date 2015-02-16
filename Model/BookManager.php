<?php

namespace N1c0\QuoteBundle\Model;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\BookEvent;
use N1c0\QuoteBundle\Event\BookPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidArgumentException;

/**
 * Abstract Book Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class BookManager implements BookManagerInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get a list of Books.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit, $offset)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return BookInterface
     */
    public function findBookById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty book instance
     *
     * @return Book
     */
    public function createBook(QuoteInterface $quote)
    {
        $class = $this->getClass();
        $book = new $class;

        $book->setQuote($quote);

        $event = new BookEvent($book);
        $this->dispatcher->dispatch(Events::BOOK_CREATE, $event);

        return $book;
    }

    /**
     * Saves a book to the persistence backend used. Each backend
     * must implement the abstract doSaveBook method which will
     * perform the saving of the book to the backend.
     *
     * @param  BookInterface         $book
     * @throws InvalidBookException when the book does not have a quote.
     */
    public function saveBook(BookInterface $book)
    {
        if (null === $book->getQuote()) {
            throw new InvalidArgumentException('The book must have a quote');
        }

        $event = new BookPersistEvent($book);
        $this->dispatcher->dispatch(Events::BOOK_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveBook($book);

        $event = new BookEvent($book);
        $this->dispatcher->dispatch(Events::BOOK_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a book.
     *
     * @abstract
     * @param BookInterface $book
     */
    abstract protected function doSaveBook(BookInterface $book);
}
