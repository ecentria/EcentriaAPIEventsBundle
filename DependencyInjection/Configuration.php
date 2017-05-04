<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     * 
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ecentria_api_events');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->scalarNode('domain_message_prefix')->defaultValue('domain.')->end()
                ->scalarNode('domain_message_serializer')->cannotBeEmpty()->end()
                ->scalarNode('requeue_as_new_producer')->defaultNull()->end()
                ->scalarNode('domain_message_class_name')
                    ->defaultValue('Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message')
                ->end()
            ->end();



        return $treeBuilder;
    }
}
