<?php

namespace N1c0\QuoteBundle\Event;

use N1c0\QuoteBundle\Model\HousepublishingInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a housepublishing.
 */
class HousepublishingEvent extends Event
{
    private $housepublishing;

    /**
     * Constructs an event.
     *
     * @param \n1c0\QuoteBundle\Model\HousepublihingInterface $housepublishing
     */
    public function __construct(HousepublishingInterface $housepublishing)
    {
        $this->housepublishing = $housepublishing;
    }

    /**
     * Returns the housepublihing for this event.
     *
     * @return \n1c0\QuoteBundle\Model\HousepublishingInterface
     */
    public function getHousepublishing()
    {
        return $this->housepublishing;
    }
}
