<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TagsEvent;
use N1c0\DissertationBundle\Model\SignedTagsInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a housePublishing using Symfony2 security component
 */
class TagsBlamerListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param LoggerInterface          $logger
     */
    public function __construct(SecurityContextInterface $securityContext = null, LoggerInterface $logger = null)
    {
        $this->securityContext = $securityContext;
        $this->logger = $logger;
    }

    /**
     * Assigns the currently logged in user to a Tags.
     *
     * @param  \N1c0\DissertationBundle\Event\TagsEvent $event
     * @return void
     */
    public function blame(TagsEvent $event)
    {
        $housePublishing = $event->getTags();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Tags Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$housePublishing instanceof SignedTagsInterface) {
            if ($this->logger) {
                $this->logger->debug("Tags does not implement SignedTagsInterface, skipping");
            }

            return;
        }

        if (null === $this->securityContext->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->securityContext->getToken()->getUser();
            $housePublishing->setAuthor($user);
            if (!$housePublishing->getAuthors()->contains($user)) {
                $housePublishing->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::HOUSEPUBLISHING_PRE_PERSIST => 'blame');
    }
}
