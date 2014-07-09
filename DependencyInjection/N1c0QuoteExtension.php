<?php

/**
 * This file is chapter of the N1c0QuoteBundle package.
 *
 * (c) 
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace N1c0\QuoteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class N1c0QuoteExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (array_key_exists('acl', $config)) {
            $this->loadAcl($container, $config);
        }

        $container->setParameter('n1c0_quote.model.quote.class', $config['class']['model']['quote']);

        $container->setParameter('n1c0_quote.model_manager_name', $config['model_manager_name']);

        $container->setParameter('n1c0_quote.form.quote.type', $config['form']['quote']['type']);

        $container->setParameter('n1c0_quote.form.quote.name', $config['form']['quote']['name']);

        $container->setAlias('n1c0_quote.form_factory.quote', $config['service']['form_factory']['quote']);

        $container->setAlias('n1c0_quote.manager.quote', $config['service']['manager']['quote']);

        // Add a condition if markup so...
        $container->setAlias('n1c0_quote.markup', new Alias($config['service']['markup'], false));
    }

    protected function loadAcl(ContainerBuilder $container, array $config)
    {
        //$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        //$loader->load('acl.xml');

        foreach (array(1 => 'create', 'view', 'edit', 'delete') as $index => $perm) {
            $container->getDefinition('n1c0_quote.acl.quote.roles')->replaceArgument($index, $config['acl_roles']['quote'][$perm]);
        }

        $container->setAlias('n1c0_quote.acl.quote', $config['service']['acl']['quote']);
    }
}
