<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Interface to be implemented by authorSrc managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface AuthorSrcManagerInterface
{
    /**
     * Get a list of AuthorSrcs.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return AuthorSrcInterface
     */
    public function findAuthorSrcById($id);

    /**
     * Returns a flat array of authorSrcs with the specified quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of AuthorSrcInterface
     */
    public function findAuthorSrcsByQuote(QuoteInterface $quote);

    /**
     * Returns an empty authorSrc instance
     *
     * @return AuthorSrc
     */
    public function createAuthorSrc(QuoteInterface $quote);

    /**
     * Saves a authorSrc
     *
     * @param  AuthorSrcInterface         $authorSrc
     */
    public function saveAuthorSrc(AuthorSrcInterface $authorSrc);
}
