<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Interface to be implemented by housepublishing managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface HousepublishingManagerInterface
{
    /**
     * Get a list of Housepublishings.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit, $offset);

    /**
     * @param  string          $id
     * @return HousepublishingInterface
     */
    public function findHousepublishingById($id);

    /**
     * Returns a flat array of housepublishings with the specified quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of HousepublishingInterface
     */
    //public function findHousepublishingsByQuote(QuoteInterface $quote);

    /**
     * Returns an empty housepublishing instance
     *
     * @return Housepublishing
     */
    public function createHousepublishing(QuoteInterface $quote);

    /**
     * Saves a housepublishing
     *
     * @param  HousepublishingInterface         $housepublishing
     */
    public function saveHousepublishing(HousepublishingInterface $housepublishing);
}
