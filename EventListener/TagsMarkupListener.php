<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TagsEvent;
use N1c0\DissertationBundle\Markup\ParserInterface;
use N1c0\DissertationBundle\Model\RawTagsInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a tags for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class TagsMarkupListener implements EventSubscriberInterface
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
     * Parses raw tags data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\DissertationBundle\Event\TagsEvent $event
     */
    public function markup(TagsEvent $event)
    {
        $tags = $event->getTags();

        if (!$tags instanceof RawTagsInterface) {
            return;
        }

        $result = $this->parser->parse($tags->getBody());
        $tags->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::TAGS_PRE_PERSIST => 'markup');
    }
}
