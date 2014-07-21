<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Interface to be implemented by tag managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface TagManagerInterface
{
    /**
     * Get a list of Tags.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return TagInterface
     */
    public function findTagById($id);

    /**
     * Returns a flat array of tags with the specified quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of TagInterface
     */
    public function findTagsByQuote(QuoteInterface $quote);

    /**
     * Returns an empty tag instance
     *
     * @return Tag
     */
    public function createTag(QuoteInterface $quote);

    /**
     * Saves a tag
     *
     * @param  TagInterface         $tag
     */
    public function saveTag(TagInterface $tag);
}
