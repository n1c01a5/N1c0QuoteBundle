<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\TagsInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a tags.
 */
class TagsEvent extends Event
{
    private $tags;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\TagsInterface $tags
     */
    public function __construct(TagsInterface $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Returns the tags for this event.
     *
     * @return \n1c0\DissertationBundle\Model\TagsInterface
     */
    public function getTags()
    {
        return $this->tags;
    }
}
