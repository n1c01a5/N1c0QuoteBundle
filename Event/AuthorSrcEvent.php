<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\AuthorSrcInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a authorSrc.
 */
class AuthorSrcEvent extends Event
{
    private $authorSrc;

    /**
     * Constructs an event.
     *
     * @param \n1c0\DissertationBundle\Model\AuthorSrcInterface $authorSrc
     */
    public function __construct(AuthorSrcInterface $authorSrc)
    {
        $this->authorSrc = $authorSrc;
    }

    /**
     * Returns the authorSrc for this event.
     *
     * @return \n1c0\DissertationBundle\Model\AuthorSrcInterface
     */
    public function getAuthorSrc()
    {
        return $this->authorSrc;
    }
}
