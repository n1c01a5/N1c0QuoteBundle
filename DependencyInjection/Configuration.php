<?php

namespace N1c0\QuoteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('n1c0_quote')
            ->children()
            
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()

                ->arrayNode('form')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('quote')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_quote_quote')->end()
                                ->scalarNode('name')->defaultValue('n1c0_quote_quote')->end()
                            ->end()
                        ->end()
                        ->arrayNode('authorsrc')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_quote_authorsrc')->end()
                                ->scalarNode('name')->defaultValue('n1c0_quote_authorsrc')->end()
                            ->end()
                        ->end()
                        ->arrayNode('housepublishing')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_quote_housepublishing')->end()
                                ->scalarNode('name')->defaultValue('n1c0_quote_housepublishing')->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('n1c0_quote_tag')->end()
                                ->scalarNode('name')->defaultValue('n1c0_quote_tag')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('class')->isRequired()
                    ->children()
                        ->arrayNode('model')->isRequired()
                            ->children()
                                ->scalarNode('quote')->isRequired()->end()
                                ->scalarNode('authorsrc')->isRequired()->end()
                                ->scalarNode('housepublishing')->isRequired()->end()
                                ->scalarNode('tag')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('acl')->end()

                ->arrayNode('acl_roles')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('quote')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('authorsrc')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('housepublishing')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('service')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('manager')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('quote')->cannotBeEmpty()->defaultValue('n1c0_quote.manager.quote.default')->end()
                                ->scalarNode('authorsrc')->cannotBeEmpty()->defaultValue('n1c0_quote.manager.authorsrc.default')->end()
                                ->scalarNode('housepublishing')->cannotBeEmpty()->defaultValue('n1c0_quote.manager.housepublishing.default')->end()
                                ->scalarNode('tag')->cannotBeEmpty()->defaultValue('n1c0_quote.manager.tag.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('acl')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('quote')->cannotBeEmpty()->defaultValue('n1c0_quote.acl.quote.security')->end()
                                ->scalarNode('authorsrc')->cannotBeEmpty()->defaultValue('n1c0_quote.acl.authorsrc.security')->end()
                                ->scalarNode('housepublishing')->cannotBeEmpty()->defaultValue('n1c0_quote.acl.housepublishing.security')->end()
                                ->scalarNode('tag')->cannotBeEmpty()->defaultValue('n1c0_quote.acl.tag.security')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_factory')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('quote')->cannotBeEmpty()->defaultValue('n1c0_quote.form_factory.quote.default')->end()
                                ->scalarNode('authorsrc')->cannotBeEmpty()->defaultValue('n1c0_quote.form_factory.authorsrc.default')->end()
                                ->scalarNode('housepublishing')->cannotBeEmpty()->defaultValue('n1c0_quote.form_factory.housepublishing.default')->end()
                                ->scalarNode('tag')->cannotBeEmpty()->defaultValue('n1c0_quote.form_factory.tag.default')->end()

                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
