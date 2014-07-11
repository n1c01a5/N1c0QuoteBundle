<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\TagsInterface;
use N1c0\DissertationBundle\Model\SignedTagsInterface;
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
class SecurityTagsAcl implements TagsAclInterface
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
     * The FQCN of the Tags object.
     *
     * @var string
     */
    protected $tagsClass;

    /**
     * The Class OID for the Tags object.
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
     * @param string                                   $tagsClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $tagsClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->tagsClass = $tagsClass;
        $this->oid               = new ObjectIdentity('class', $this->tagsClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Tags.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canView(TagsInterface $tags)
    {
        return $this->securityContext->isGranted('VIEW', $tags);
    }

    /**
     * Checks if the Security token is allowed to edit the specified Tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canEdit(TagsInterface $tags)
    {
        return $this->securityContext->isGranted('EDIT', $tags);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canDelete(TagsInterface $tags)
    {
        return $this->securityContext->isGranted('DELETE', $tags);
    }

    /**
     * Sets the default object Acl entry for the supplied Tags.
     *
     * @param  TagsInterface $tags
     * @return void
     */
    public function setDefaultAcl(TagsInterface $tags)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($tags);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($tags instanceof SignedTagsInterface &&
            null !== $tags->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($tags->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Tags class.
     *
     * This needs to be re-run whenever the Tags class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->tagsClass);

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
     * `fos:tags:installAces --flush` command
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
     * Removes fallback Acl entries for the Tags class.
     *
     * This should be run when uninstalling the TagsBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->tagsClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

