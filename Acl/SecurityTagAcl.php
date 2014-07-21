<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\TagInterface;
use N1c0\DissertationBundle\Model\SignedTagInterface;
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
class SecurityTagAcl implements TagAclInterface
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
     * The FQCN of the Tag object.
     *
     * @var string
     */
    protected $tagClass;

    /**
     * The Class OID for the Tag object.
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
     * @param string                                   $tagClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $tagClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->tagClass = $tagClass;
        $this->oid               = new ObjectIdentity('class', $this->tagClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Tag.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canView(TagInterface $tag)
    {
        return $this->securityContext->isGranted('VIEW', $tag);
    }

    /**
     * Checks if the Security token is allowed to edit the specified Tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canEdit(TagInterface $tag)
    {
        return $this->securityContext->isGranted('EDIT', $tag);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canDelete(TagInterface $tag)
    {
        return $this->securityContext->isGranted('DELETE', $tag);
    }

    /**
     * Sets the default object Acl entry for the supplied Tag.
     *
     * @param  TagInterface $tag
     * @return void
     */
    public function setDefaultAcl(TagInterface $tag)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($tag);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($tag instanceof SignedTagInterface &&
            null !== $tag->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($tag->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Tag class.
     *
     * This needs to be re-run whenever the Tag class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->tagClass);

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
     * `fos:tag:installAces --flush` command
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
     * Removes fallback Acl entries for the Tag class.
     *
     * This should be run when uninstalling the TagBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->tagClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

