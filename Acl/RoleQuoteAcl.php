<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\QuoteInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleQuoteAcl implements QuoteAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Quote object.
     *
     * @var string
     */
    private $quoteClass;

    /**
     * The role that will grant create permission for a quote.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a quote.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a quote.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a quote.
     *
     * @var string
     */
    private $deleteRole;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param string                   $createRole
     * @param string                   $viewRole
     * @param string                   $editRole
     * @param string                   $deleteRole
     * @param string                   $quoteClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $quoteClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->quoteClass      = $quoteClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Quote.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canView(QuoteInterface $quote)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent quote.
     *
     * @param  QuoteInterface|null $parent
     * @return boolean
     */
    public function canReply(QuoteInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canEdit(QuoteInterface $quote)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canDelete(QuoteInterface $quote)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  QuoteInterface $quote
     * @return void
     */
    public function setDefaultAcl(QuoteInterface $quote)
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function installFallbackAcl()
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {

    }
}
