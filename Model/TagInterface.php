<?php

namespace N1c0\QuoteBundle\Model;

Interface TagInterface
{
    /**
     * @return mixed unique ID for this tag
     */
    public function getId();
    
    /**
     * Set title
     *
     * @param string $title
     * @return TagInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();
}
