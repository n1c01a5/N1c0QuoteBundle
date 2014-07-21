<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\TagInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a tag.
 */
class TagEvent extends Event
{
    private $tag;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\TagInterface $tag
     */
    public function __construct(TagInterface $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Returns the tag for this event.
     *
     * @return \n1c0\DissertationBundle\Model\TagInterface
     */
    public function getTag()
    {
        return $this->tag;
    }
}
