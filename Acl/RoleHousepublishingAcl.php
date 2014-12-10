<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\HousepublishingInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleHousepublishingAcl implements HousepublishingAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Housepublishing object.
     *
     * @var string
     */
    private $housepublishingClass;

    /**
     * The role that will grant create permission for a housepublishing.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a housepublishing.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a housepublishing.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a housepublishing.
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
     * @param string                   $housepublishingClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $housepublishingClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->housepublishingClass      = $housepublishingClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Housepublishing.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canView(HousepublishingInterface $housepublishing)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent housepublishing.
     *
     * @param  HousepublishingInterface|null $parent
     * @return boolean
     */
    public function canReply(HousepublishingInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canEdit(HousepublishingInterface $housepublishing)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canDelete(HousepublishingInterface $housepublishing)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return void
     */
    public function setDefaultAcl(HousepublishingInterface $housepublishing)
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
