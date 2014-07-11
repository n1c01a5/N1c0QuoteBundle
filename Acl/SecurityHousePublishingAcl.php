<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\HousePublishingInterface;
use N1c0\DissertationBundle\Model\SignedHousePublishingInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements ACL checking using the Symfony2 Security component
 */
class SecurityHousePublishingAcl implements HousePublishingAclInterface
{
    /**
     * Used to retrieve ObjectIdentity instances for objects.
     *
     * @var ObjectIdentityRetrievalStrategyInterface
     */
    protected $objectRetrieval;

    /**
     * The AclProvider.
     *
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * The FQCN of the HousePublishing object.
     *
     * @var string
     */
    protected $housePublishingClass;

    /**
     * The Class OID for the HousePublishing object.
     *
     * @var ObjectIdentity
     */
    protected $oid;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface                 $securityContext
     * @param ObjectIdentityRetrievalStrategyInterface $objectRetrieval
     * @param MutableAclProviderInterface              $aclProvider
     * @param string                                   $housePublishingClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $housePublishingClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->housePublishingClass = $housePublishingClass;
        $this->oid               = new ObjectIdentity('class', $this->housePublishingClass);
    }

    /**
     * Checks if the Security token is allowed to create a new HousePublishing.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified HousePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canView(HousePublishingInterface $housePublishing)
    {
        return $this->securityContext->isGranted('VIEW', $housePublishing);
    }

    /**
     * Checks if the Security token is allowed to edit the specified HousePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canEdit(HousePublishingInterface $housePublishing)
    {
        return $this->securityContext->isGranted('EDIT', $housePublishing);
    }

    /**
     * Checks if the Security token is allowed to delete the specified HousePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return boolean
     */
    public function canDelete(HousePublishingInterface $housePublishing)
    {
        return $this->securityContext->isGranted('DELETE', $housePublishing);
    }

    /**
     * Sets the default object Acl entry for the supplied HousePublishing.
     *
     * @param  HousePublishingInterface $housePublishing
     * @return void
     */
    public function setDefaultAcl(HousePublishingInterface $housePublishing)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($housePublishing);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($housePublishing instanceof SignedHousePublishingInterface &&
            null !== $housePublishing->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($housePublishing->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the HousePublishing class.
     *
     * This needs to be re-run whenever the HousePublishing class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->housePublishingClass);

        try {
            $acl = $this->aclProvider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            return;
        }

        $this->doInstallFallbackAcl($acl, new MaskBuilder());
        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs the default Class Ace entries into the provided $acl object.
     *
     * Override this method in a subclass to change what permissions are defined.
     * Once this method has been overridden you need to run the
     * `fos:housePublishing:installAces --flush` command
     *
     * @param  AclInterface $acl
     * @param  MaskBuilder  $builder
     * @return void
     */
    protected function doInstallFallbackAcl(AclInterface $acl, MaskBuilder $builder)
    {
        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPER_ADMIN'), $builder->get());

        $builder->reset();
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());
    }

    /**
     * Removes fallback Acl entries for the HousePublishing class.
     *
     * This should be run when uninstalling the HousePublishingBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->housePublishingClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

