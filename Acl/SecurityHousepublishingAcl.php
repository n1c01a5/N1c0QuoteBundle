<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\HousepublishingInterface;
use N1c0\QuoteBundle\Model\SignedHousepublishingInterface;
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
class SecurityHousepublishingAcl implements HousepublishingAclInterface
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
     * The FQCN of the Housepublishing object.
     *
     * @var string
     */
    protected $housepublishingClass;

    /**
     * The Class OID for the Housepublishing object.
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
     * @param string                                   $housepublishingClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $housepublishingClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->housepublishingClass = $housepublishingClass;
        $this->oid               = new ObjectIdentity('class', $this->housepublishingClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Housepublishing.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canView(HousepublishingInterface $housepublishing)
    {
        return $this->securityContext->isGranted('VIEW', $housepublishing);
    }

    /**
     * Checks if the Security token is allowed to edit the specified Housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canEdit(HousepublishingInterface $housepublishing)
    {
        return $this->securityContext->isGranted('EDIT', $housepublishing);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return boolean
     */
    public function canDelete(HousepublishingInterface $housepublishing)
    {
        return $this->securityContext->isGranted('DELETE', $housepublishing);
    }

    /**
     * Sets the default object Acl entry for the supplied Housepublishing.
     *
     * @param  HousepublishingInterface $housepublishing
     * @return void
     */
    public function setDefaultAcl(HousepublishingInterface $housepublishing)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($housepublishing);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($housepublishing instanceof SignedHousepublishingInterface &&
            null !== $housepublishing->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($housepublishing->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Housepublishing class.
     *
     * This needs to be re-run whenever the Housepublishing class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->housepublishingClass);

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
     * `fos:housepublishing:installAces --flush` command
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
     * Removes fallback Acl entries for the Housepublishing class.
     *
     * This should be run when uninstalling the HousepublishingBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->housepublishingClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

