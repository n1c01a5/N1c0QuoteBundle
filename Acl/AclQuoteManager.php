<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\QuoteManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of QuoteManagerInterface and
 * performs Acl checks with the configured Quote Acl service.
 */
class AclQuoteManager implements QuoteManagerInterface
{
    /**
     * The QuoteManager instance to be wrapped with ACL.
     *
     * @var QuoteManagerInterface
     */
    protected $realManager;

    /**
     * The QuoteAcl instance for checking permissions.
     *
     * @var QuoteAclInterface
     */
    protected $quoteAcl;

    /**
     * Constructor.
     *
     * @param QuoteManagerInterface $quoteManager The concrete QuoteManager service
     * @param QuoteAclInterface     $quoteAcl     The Quote Acl service
     */
    public function __construct(QuoteManagerInterface $quoteManager, QuoteAclInterface $quoteAcl)
    {
        $this->realManager = $quoteManager;
        $this->quoteAcl  = $quoteAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $quotes = $this->realManager->all();

        if (!$this->authorizeViewQuote($quotes)) {
            throw new AccessDeniedException();
        }

        return $quotes;
    }

    /**
     * {@inheritDoc}
     */
    public function findQuoteBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findQuotesBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllQuotes(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveQuote(QuoteInterface $quote)
    {
        if (!$this->quoteAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newQuote = $this->isNewQuote($quote);

        if (!$newQuote && !$this->quoteAcl->canEdit($quote)) {
            throw new AccessDeniedException();
        }

        if (($quote::STATE_DELETED === $quote->getState() || $quote::STATE_DELETED === $quote->getPreviousState())
            && !$this->quoteAcl->canDelete($quote)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveQuote($quote);

        if ($newQuote) {
            $this->quoteAcl->setDefaultAcl($quote);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findQuoteById($id)
    {
        $quote = $this->realManager->findQuoteById($id);

        if (null !== $quote && !$this->quoteAcl->canView($quote)) {
            throw new AccessDeniedException();
        }

        return $quote;
    }

    /**
     * {@inheritDoc}
     */
    public function createQuote($id = null)
    {
        return $this->realManager->createQuote($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewQuote(QuoteInterface $quote)
    {
        return $this->realManager->isNewQuote($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the quote have appropriate view permissions.
     *
     * @param  array   $quotes A comment tree
     * @return boolean
     */
    protected function authorizeViewQuote(array $quotes)
    {
        foreach ($quotes as $quote) {
            if (!$this->quoteAcl->canView($quote)) {
                return false;
            }
        }

        return true;
    }
}
