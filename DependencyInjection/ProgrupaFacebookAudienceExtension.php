<?php


namespace Progrupa\FacebookAudienceBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ProgrupaFacebookAudienceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('progrupa.facebook_audience.client_id', $config['client_id']);
        $container->setParameter('progrupa.facebook_audience.client_secret', $config['client_secret']);
        $container->setParameter('progrupa.facebook_audience.marketing_token', $config['marketing_token']);
        $container->setParameter('progrupa.facebook_audience.business_id', $config['business_id']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

}