<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Interface to be implemented by element quote managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to element quote should happen through this interface.
 */
interface QuoteManagerInterface
{
    /**
     * Get a list of Quotes.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return QuoteInterface
     */
    public function findQuoteById($id);

    /**
     * Finds one element quote by the given criteria
     *
     * @param  array           $criteria
     * @return QuoteInterface
     */
    public function findQuoteBy(array $criteria);

    /**
     * Finds quotes by the given criteria
     *
     * @param array $criteria
     *
     * @return array of QuoteInterface
     */
    public function findQuotesBy(array $criteria);

    /**
     * Finds all quotes.
     *
     * @return array of QuoteInterface
     */
    public function findAllQuotes();

    /**
     * Creates an empty element quote instance
     *
     * @param  bool   $id
     * @return Quote
     */
    public function createQuote($id = null);

    /**
     * Saves a quote
     *
     * @param QuoteInterface $quote
     */
    public function saveQuote(QuoteInterface $quote);

    /**
     * Checks if the quote was already persisted before, or if it's a new one.
     *
     * @param QuoteInterface $quote
     *
     * @return boolean True, if it's a new quote
     */
    public function isNewQuote(QuoteInterface $quote);

    /**
     * Returns the element quote fully qualified class name
     *
     * @return string
     */
    public function getClass();
}
