<?php

namespace N1c0\DissertationBundle\Model;

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
     * Returns a flat array of housePublishings with the specified dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of HousePublishingInterface
     */
    public function findHousePublishingsByDissertation(DissertationInterface $dissertation);

    /**
     * Returns an empty housePublishing instance
     *
     * @return HousePublishing
     */
    public function createHousePublishing(DissertationInterface $dissertation);

    /**
     * Saves a housePublishing
     *
     * @param  HousePublishingInterface         $housePublishing
     */
    public function saveHousePublishing(HousePublishingInterface $housePublishing);
}
