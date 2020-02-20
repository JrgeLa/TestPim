<?php


namespace TheAkademy\RuleActionBundle\DependencyInjecction;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TheAkademyRuleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        # $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        # $loader->load('example_rules.yml');
    }
}