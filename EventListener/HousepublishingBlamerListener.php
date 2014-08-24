<?php

namespace N1c0\QuoteBundle\EventListener;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\HousepublishingEvent;
use N1c0\QuoteBundle\Model\SignedHousepublishingInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a housepublishing using Symfony2 security component
 */
class HousepublishingBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Housepublishing.
     *
     * @param  \N1c0\QuoteBundle\Event\HousepublishingEvent $event
     * @return void
     */
    public function blame(HousepublishingEvent $event)
    {
        $housepublishing = $event->getHousepublishing();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Housepublishing Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$housepublishing instanceof SignedHousepublishingInterface) {
            if ($this->logger) {
                $this->logger->debug("Housepublishing does not implement SignedHousepublishingInterface, skipping");
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
            $housepublishing->setAuthor($user);
            if (!$housepublishing->getAuthors()->contains($user)) {
                $housepublishing->addAuthor($user);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::HOUSEPUBLISHING_PRE_PERSIST => 'blame');
    }
}
