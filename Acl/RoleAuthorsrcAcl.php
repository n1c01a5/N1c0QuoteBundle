<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\AuthorsrcBundle\Model\AuthorsrcInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleAuthorsrcAcl implements AuthorsrcAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Authorsrc object.
     *
     * @var string
     */
    private $authorsrcClass;

    /**
     * The role that will grant create permission for a authorsrc.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a authorsrc.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a authorsrc.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a authorsrc.
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
     * @param string                   $authorsrcClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $authorsrcClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->authorsrcClass      = $authorsrcClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Authorsrc.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canView(AuthorsrcInterface $authorsrc)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent authorsrc.
     *
     * @param  AuthorsrcInterface|null $parent
     * @return boolean
     */
    public function canReply(AuthorsrcInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canEdit(AuthorsrcInterface $authorsrc)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canDelete(AuthorsrcInterface $authorsrc)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return void
     */
    public function setDefaultAcl(AuthorsrcInterface $authorsrc)
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
