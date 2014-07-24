<?php

namespace N1c0\QuoteBundle\Event;

use N1c0\QuoteBundle\Model\HousePusblishingInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a housePusblishing.
 */
class HousePusblishingEvent extends Event
{
    private $housePusblishing;

    /**
     * Constructs an event.
     *
     * @param \n1c0\QuoteBundle\Model\HousePusblishingInterface $housePusblishing
     */
    public function __construct(HousePusblishingInterface $housePusblishing)
    {
        $this->housePusblishing = $housePusblishing;
    }

    /**
     * Returns the housePusblishing for this event.
     *
     * @return \n1c0\QuoteBundle\Model\HousePusblishingInterface
     */
    public function getHousePusblishing()
    {
        return $this->housePusblishing;
    }
}
