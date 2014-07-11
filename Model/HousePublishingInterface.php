<?php

namespace N1c0\DissertationBundle\Model;

Interface HousePublishingInterface
{
    /**
     * @return mixed unique ID for this housePublishing
     */
    public function getId();
    
    /**
     * Set title
     *
     * @param string $title
     * @return HousePublishingInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set body
     *
     * @param string $body
     * @return HousePublishingInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody();

    /**
     * @return DissertationInterface
     */
    public function getDissertation();

    /**
     * @param DissertationInterface $dissertation
     */
    public function setDissertation(DissertationInterface $dissertation);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}
