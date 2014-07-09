<?php

namespace N1c0\QuoteBundle\EventListener;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\QuoteEvent;
use N1c0\QuoteBundle\Model\SignedQuoteInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a quote using Symfony2 security component
 */
class QuoteBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Quote.
     *
     * @param  \N1c0\QuoteBundle\Event\QuoteEvent $event
     * @return void
     */
    public function blame(QuoteEvent $event)
    {
        $quote = $event->getQuote();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Quote Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$quote instanceof SignedQuoteInterface) {
            if ($this->logger) {
                $this->logger->debug("Quote does not implement SignedQuoteInterface, skipping");
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
            $quote->setAuthor($user);
            if (!$quote->getAuthors()->contains($user)) {
                $quote->addAuthor($user);
            }

        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::QUOTE_PRE_PERSIST => 'blame');
    }
}
