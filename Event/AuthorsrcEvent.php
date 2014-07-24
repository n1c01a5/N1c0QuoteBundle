<?php

namespace N1c0\QuoteBundle\Event;

use N1c0\QuoteBundle\Model\AuthorsrcInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a authorsrc.
 */
class AuthorsrcEvent extends Event
{
    private $authorsrc;

    /**
     * Constructs an event.
     *
     * @param \n1c0\QuoteBundle\Model\AuthorsrcInterface $authorsrc
     */
    public function __construct(AuthorsrcInterface $authorsrc)
    {
        $this->authorsrc = $authorsrc;
    }

    /**
     * Returns the authorsrc for this event.
     *
     * @return \n1c0\QuoteBundle\Model\AuthorsrcInterface
     */
    public function getAuthorsrc()
    {
        return $this->authorsrc;
    }
}
