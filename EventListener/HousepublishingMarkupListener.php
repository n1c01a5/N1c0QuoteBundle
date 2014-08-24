<?php

namespace N1c0\QuoteBundle\EventListener;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\HousepublishingEvent;
use N1c0\QuoteBundle\Markup\ParserInterface;
use N1c0\QuoteBundle\Model\RawHousepublishingInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a housepublishing for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class HousepublishingMarkupListener implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \N1c0\QuoteBundle\Markup\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses raw housepublishing data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\QuoteBundle\Event\HousepublishingEvent $event
     */
    public function markup(HousepublishingEvent $event)
    {
        $housepublishing = $event->getHousepublishing();

        if (!$housepublishing instanceof RawHousepublishingInterface) {
            return;
        }

        $result = $this->parser->parse($housepublishing->getBody());
        $housepublishing->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::HOUSEPUBLISHING_PRE_PERSIST => 'markup');
    }
}
