<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AzarniewiczSyliusInPostPluginExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('azarniewicz_sylius_inpost.shipping_method_code', $config['shipping_method_code']);
        $container->setParameter('azarniewicz_sylius_inpost.api_base_url', rtrim($config['api_base_url'], '/'));

        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__) . '/config'));
        $loader->load('services.yaml');
    }

    public function getAlias(): string
    {
        return 'azarniewicz_sylius_inpost';
    }

    public function prepend(ContainerBuilder $container): void
    {
        /** @var array<string, array<string, string>> $metadata */
        $metadata = $container->getParameter('kernel.bundles_metadata');
        $bundlePath = $metadata['AzarniewiczSyliusInPostPlugin']['path'];

        $doctrineDir = $bundlePath . '/Resources/config/doctrine';
        $viewsDir = $bundlePath . '/Resources/views';
        $translationsDir = $bundlePath . '/Resources/translations';

        if ($container->hasExtension('doctrine')) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'mappings' => [
                        'AzarniewiczSyliusInPostPlugin' => [
                            'type' => 'xml',
                            'dir' => $doctrineDir,
                            'is_bundle' => false,
                            'prefix' => 'Azarniewicz\SyliusInPostPlugin\Entity',
                        ],
                    ],
                ],
            ]);
        }

        if ($container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', [
                'paths' => [
                    $viewsDir => 'AzarniewiczSyliusInPostPlugin',
                ],
                'globals' => [
                    'azarniewicz_sylius_inpost_shipping_method_code' => '%azarniewicz_sylius_inpost.shipping_method_code%',
                    'azarniewicz_sylius_inpost_api_base_url' => '%azarniewicz_sylius_inpost.api_base_url%',
                ],
            ]);
        }

        if ($container->hasExtension('framework')) {
            $container->prependExtensionConfig('framework', [
                'translator' => [
                    'paths' => [$translationsDir],
                ],
                'assets' => [
                    'packages' => [
                        'inpost_shop' => [
                            'json_manifest_path' => '%kernel.project_dir%/public/build/azarniewicz/inpost/shop/manifest.json',
                        ],
                        'inpost_admin' => [
                            'json_manifest_path' => '%kernel.project_dir%/public/build/azarniewicz/inpost/admin/manifest.json',
                        ],
                    ],
                ],
            ]);
        }

        if ($container->hasExtension('webpack_encore')) {
            $container->prependExtensionConfig('webpack_encore', [
                'builds' => [
                    'inpost_admin' => '%kernel.project_dir%/public/build/azarniewicz/inpost/admin',
                    'inpost_shop' => '%kernel.project_dir%/public/build/azarniewicz/inpost/shop',
                ],
            ]);
        }

        if ($container->hasExtension('sylius_twig_hooks')) {
            $container->prependExtensionConfig('sylius_twig_hooks', [
                'hooks' => [
                    'sylius_shop.base#stylesheets' => [
                        'azarniewicz_inpost_shop_styles' => [
                            'template' => '@AzarniewiczSyliusInPostPlugin/Shop/_styles.html.twig',
                            'priority' => -100,
                        ],
                    ],
                    'sylius_shop.base#javascripts' => [
                        'azarniewicz_inpost_shop_scripts' => [
                            'template' => '@AzarniewiczSyliusInPostPlugin/Shop/_scripts.html.twig',
                            'priority' => -100,
                        ],
                    ],
                    'sylius_admin.base#stylesheets' => [
                        'azarniewicz_inpost_admin_styles' => [
                            'template' => '@AzarniewiczSyliusInPostPlugin/Admin/_styles.html.twig',
                            'priority' => -100,
                        ],
                    ],
                    'sylius_admin.base#javascripts' => [
                        'azarniewicz_inpost_admin_scripts' => [
                            'template' => '@AzarniewiczSyliusInPostPlugin/Admin/_scripts.html.twig',
                            'priority' => -100,
                        ],
                    ],
                ],
            ]);
        }
    }
}
