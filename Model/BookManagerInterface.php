<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Interface to be implemented by book managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface BookManagerInterface
{
    /**
     * Get a list of Books.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit, $offset);

    /**
     * @param  string          $id
     * @return BookInterface
     */
    public function findBookById($id);

    /**
     * Returns a flat array of books with the specified quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of BookInterface
     */
    //public function findBooksByQuote(QuoteInterface $quote);

    /**
     * Returns an empty book instance
     *
     * @return Book
     */
    public function createBook(QuoteInterface $quote);

    /**
     * Saves a book
     *
     * @param  BookInterface         $book
     */
    public function saveBook(BookInterface $book);
}
