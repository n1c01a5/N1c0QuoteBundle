<?php

namespace N1c0\DissertationBundle\Model;

/**
 * Interface to be implemented by tags managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 */
interface TagsManagerInterface
{
    /**
     * Get a list of Tagss.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param  string          $id
     * @return TagsInterface
     */
    public function findTagsById($id);

    /**
     * Returns a flat array of tagss with the specified dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of TagsInterface
     */
    public function findTagssByDissertation(DissertationInterface $dissertation);

    /**
     * Returns an empty tags instance
     *
     * @return Tags
     */
    public function createTags(DissertationInterface $dissertation);

    /**
     * Saves a tags
     *
     * @param  TagsInterface         $tags
     */
    public function saveTags(TagsInterface $tags);
}
