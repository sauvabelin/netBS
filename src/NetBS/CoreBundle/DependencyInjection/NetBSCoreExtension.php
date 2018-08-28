<?php

namespace NetBS\CoreBundle\DependencyInjection;

use NetBS\CoreBundle\Mailer\MailChannel;
use NetBS\CoreBundle\Model\MailerConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NetBSCoreExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $mailConfig = new Definition(MailerConfig::class);
        $mailConfig->setArguments([
            $config['mailer']['subject_prefix'],
            $config['mailer']['default_from']
        ]);

        $container->setDefinition('netbs.core.mailer.config', $mailConfig);

        $container->getDefinition('netbs.mailer')->setArguments([
            new Reference('netbs.core.mailer.config'),
            new Reference('twig'),
            new Reference('mailer')
        ]);

        foreach($config['mailer']['channels'] as $alias => $params) {

            $definition = new Definition(MailChannel::class);
            $definition->setArguments([
                new Reference('netbs.core.mailer.config'),
                $alias,
                $params['from'],
                $params['subject'],
                $params['template']
            ]);

            $container->setDefinition('netbs.core.mailer.channel_' . $alias, $definition);
        }
    }
}
