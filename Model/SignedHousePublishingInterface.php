<?php

namespace N1c0\DissertationBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A signed housePublishing is bound to a FOS\UserBundle User model.
 */
interface SignedHousePublishingInterface extends HousePublishingInterface
{
    /**
     * Add user 
     *
     * @param Application\UserBundle\Entity\User $user
     */
    public function addAuthor(\Application\UserBundle\Entity\User $user);

    /**
     * Remove user
     *
     * @param Application\UserBundle\Entity\User $user
     */
    public function removeUser(\Application\UserBundle\Entity\User $user);

    /**
     * Gets the authors of the HousePublishing
     *
     * @return UserInterface
     */
    public function getAuthors();

    /**
     * Gets the last author of the HousePublishing
     *
     * @return UserInterface
     */
    public function getAuthor();
}

