<?php

namespace Pbxg33k\FlareSolverrBundle;

use Pbxg33k\FlareSolverrBundle\Client\FlareSolverrClient;
use Pbxg33k\FlareSolverrBundle\Command\HealthCheckCommand;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class FlareSolverrBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('client_config')
                    ->children()
                        ->scalarNode('url')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('The URL of the FlareSolverr server.')
                        ->end() // url
                        ->arrayNode('session')
                            ->children()
                                ->scalarNode('id')
                                    ->defaultNull()
                                    ->info('The session ID to use for requests.')
                                ->end() // id
                                ->integerNode('ttl_timeout')
                                    ->defaultValue(120)
                                    ->info('The session TTL timeout in seconds.')
                                ->end() // ttl_timeout
                            ->end() // children
                        ->end() // session
                    ->end() // children
                ->end() // client
            ->end() // rootNode
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/*.yml');

        $container->services()
            ->get('flare_solverr.client')
            ->class(FlareSolverrClient::class)
            ->arg('$flareSolverrRootUrl', $config['client_config']['url']);

        $container->services()
            ->get('flare_solverr.command.healthcheck')
            ->class(HealthCheckCommand::class)
            ->arg('$flareSolverrRootUrl', $config['client_config']['url']);
    }
}
