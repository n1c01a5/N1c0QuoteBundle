<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Interface to be implemented by housePublishing managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface HousePublishingManagerInterface
{
    /**
     * Get a list of HousePublishings.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return HousePublishingInterface
     */
    public function findHousePublishingById($id);

    /**
     * Returns a flat array of housePublishings with the specified quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of HousePublishingInterface
     */
    public function findHousePublishingsByQuote(QuoteInterface $quote);

    /**
     * Returns an empty housePublishing instance
     *
     * @return HousePublishing
     */
    public function createHousePublishing(QuoteInterface $quote);

    /**
     * Saves a housePublishing
     *
     * @param  HousePublishingInterface         $housePublishing
     */
    public function saveHousePublishing(HousePublishingInterface $housePublishing);
}
