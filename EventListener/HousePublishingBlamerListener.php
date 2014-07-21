<?php

namespace N1c0\DissertationBundle\EventListener;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TagEvent;
use N1c0\DissertationBundle\Model\SignedTagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a housePublishing using Symfony2 security component
 */
class TagBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Tag.
     *
     * @param  \N1c0\DissertationBundle\Event\TagEvent $event
     * @return void
     */
    public function blame(TagEvent $event)
    {
        $housePublishing = $event->getTag();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Tag Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$housePublishing instanceof SignedTagInterface) {
            if ($this->logger) {
                $this->logger->debug("Tag does not implement SignedTagInterface, skipping");
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
