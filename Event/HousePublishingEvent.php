<?php

namespace N1c0\DissertationBundle\Event;

use N1c0\DissertationBundle\Model\HousePusblishingInterface;
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
     * @param \n1c0\DissertationBundle\Model\HousePusblishingInterface $housePusblishing
     */
    public function __construct(HousePusblishingInterface $housePusblishing)
    {
        $this->housePusblishing = $housePusblishing;
    }

    /**
     * Returns the housePusblishing for this event.
     *
     * @return \n1c0\DissertationBundle\Model\HousePusblishingInterface
     */
    public function getHousePusblishing()
    {
        return $this->housePusblishing;
    }
}
