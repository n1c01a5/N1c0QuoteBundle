<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\TagsInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface TagsAclInterface
{
    /**
     * Checks if the user should be able to create a tags.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canView(TagsInterface $tags);

    /**
     * Checks if the user should be able to edit a tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canEdit(TagsInterface $tags);

    /**
     * Checks if the user should be able to delete a tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canDelete(TagsInterface $tags);

    /**
     * Sets the default Acl permissions on a tags.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new TagsInterface instances.
     *
     * @param  TagsInterface $tags
     * @return void
     */
    public function setDefaultAcl(TagsInterface $tags);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    public function installFallbackAcl();

    /**
     * Removes default Acl entries
     * @return void
     */
    public function uninstallFallbackAcl();
}
