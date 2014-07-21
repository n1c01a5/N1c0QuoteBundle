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
        $container->setParameter('n1c0_quote.model.authorsrc.class', $config['class']['model']['authorsrc']);
        $container->setParameter('n1c0_quote.model.housepublishing.class', $config['class']['model']['housepublishing']);
        $container->setParameter('n1c0_quote.model.tag.class', $config['class']['model']['tag']);

        $container->setParameter('n1c0_quote.model_manager_name', $config['model_manager_name']);

        $container->setParameter('n1c0_quote.form.quote.type', $config['form']['quote']['type']);
        $container->setParameter('n1c0_quote.form.authorsrc.type', $config['form']['authorsrc']['type']);
        $container->setParameter('n1c0_quote.form.housepublishing.type', $config['form']['housepublishing']['type']);
        $container->setParameter('n1c0_quote.form.tag.type', $config['form']['tag']['type']);

        $container->setParameter('n1c0_quote.form.quote.name', $config['form']['quote']['name']);
        $container->setParameter('n1c0_quote.form.authorsrc.name', $config['form']['authorsrc']['name']);
        $container->setParameter('n1c0_quote.form.housepublishing.name', $config['form']['housepublishing']['name']);
        $container->setParameter('n1c0_quote.form.tag.name', $config['form']['tag']['name']);

        $container->setAlias('n1c0_quote.form_factory.quote', $config['service']['form_factory']['quote']);
        $container->setAlias('n1c0_quote.form_factory.authorsrc', $config['service']['form_factory']['authorsrc']);
        $container->setAlias('n1c0_quote.form_factory.housepublishing', $config['service']['form_factory']['housepublishing']);
        $container->setAlias('n1c0_quote.form_factory.tag', $config['service']['form_factory']['tag']);

        $container->setAlias('n1c0_quote.manager.quote', $config['service']['manager']['quote']);
        $container->setAlias('n1c0_quote.manager.authorsrc', $config['service']['manager']['authorsrc']);
        $container->setAlias('n1c0_quote.manager.housepublishing', $config['service']['manager']['housepublishing']);
        $container->setAlias('n1c0_quote.manager.tag', $config['service']['manager']['tag']);
    }

    protected function loadAcl(ContainerBuilder $container, array $config)
    {
        //$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        //$loader->load('acl.xml');

        foreach (array(1 => 'create', 'view', 'edit', 'delete') as $index => $perm) {
            $container->getDefinition('n1c0_quote.acl.quote.roles')->replaceArgument($index, $config['acl_roles']['quote'][$perm]);
            $container->getDefinition('n1c0_quote.acl.authorsrc.roles')->replaceArgument($index, $config['acl_roles']['authorsrc'][$perm]);
            $container->getDefinition('n1c0_quote.acl.housepublishing.roles')->replaceArgument($index, $config['acl_roles']['housepublishing'][$perm]);
            $container->getDefinition('n1c0_quote.acl.tag.roles')->replaceArgument($index, $config['acl_roles']['tag'][$perm]);
        }

        $container->setAlias('n1c0_quote.acl.quote', $config['service']['acl']['quote']);
        $container->setAlias('n1c0_quote.acl.authorsrc', $config['service']['acl']['authorsrc']);
        $container->setAlias('n1c0_quote.acl.housepublishing', $config['service']['acl']['housepublishing']);
        $container->setAlias('n1c0_quote.acl.tag', $config['service']['acl']['tag']);
    }
}
