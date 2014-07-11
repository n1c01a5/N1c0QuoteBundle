<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TagsEvent;
use N1c0\DissertationBundle\Markup\ParserInterface;
use N1c0\DissertationBundle\Model\RawTagsInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a housePublishing for markup and sets the result
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
     * Parses raw housePublishing data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\DissertationBundle\Event\TagsEvent $event
     */
    public function markup(TagsEvent $event)
    {
        $housePublishing = $event->getTags();

        if (!$housePublishing instanceof RawTagsInterface) {
            return;
        }

        $result = $this->parser->parse($housePublishing->getBody());
        $housePublishing->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::HOUSEPUBLISHING_PRE_PERSIST => 'markup');
    }
}
