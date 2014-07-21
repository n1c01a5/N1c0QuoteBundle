<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TagEvent;
use N1c0\DissertationBundle\Markup\ParserInterface;
use N1c0\DissertationBundle\Model\RawTagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a tag for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class TagMarkupListener implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \N1c0\DissertationBundle\Markup\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses raw tag data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\DissertationBundle\Event\TagEvent $event
     */
    public function markup(TagEvent $event)
    {
        $tag = $event->getTag();

        if (!$tag instanceof RawTagInterface) {
            return;
        }

        $result = $this->parser->parse($tag->getBody());
        $tag->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::TAG_PRE_PERSIST => 'markup');
    }
}
