<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\AuthorSrcBundle\Model\AuthorSrcInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleAuthorSrcAcl implements AuthorSrcAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the AuthorSrc object.
     *
     * @var string
     */
    private $authorSrcClass;

    /**
     * The role that will grant create permission for a authorSrc.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a authorSrc.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a authorSrc.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a authorSrc.
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
     * @param string                   $authorSrcClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $authorSrcClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->authorSrcClass      = $authorSrcClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new AuthorSrc.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified AuthorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canView(AuthorSrcInterface $authorSrc)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent authorSrc.
     *
     * @param  AuthorSrcInterface|null $parent
     * @return boolean
     */
    public function canReply(AuthorSrcInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied AuthorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canEdit(AuthorSrcInterface $authorSrc)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific AuthorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canDelete(AuthorSrcInterface $authorSrc)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return void
     */
    public function setDefaultAcl(AuthorSrcInterface $authorSrc)
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
