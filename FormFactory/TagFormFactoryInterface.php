<?php

namespace N1c0\QuoteBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Tag form creator
 */
interface TagFormFactoryInterface
{
    /**
     * Creates a chapter form
     *
     * @return FormInterface
     */
    public function createForm();
}
