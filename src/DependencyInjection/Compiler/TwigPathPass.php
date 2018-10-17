<?php

namespace AlterPHP\EasyAdminExtensionBundle\DependencyInjection\Compiler;

use AlterPHP\EasyAdminExtensionBundle\EasyAdminExtensionBundle;
use AlterPHP\EasyAdminMongoOdmBundle\EasyAdminMongoOdmBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TwigPathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $twigLoaderFilesystemId = $container->getAlias('twig.loader')->__toString();
        $twigLoaderFilesystemDefinition = $container->getDefinition($twigLoaderFilesystemId);

        // Replaces native EasyAdmin templates
        $this->coverNamespace($twigLoaderFilesystemDefinition, 'EasyAdmin', EasyAdminBundle::class);

        // CHECK_MONGO_ODM
        $mongoOdmBundleClassExists = \class_exists(EasyAdminMongoOdmBundle::class);
        $mongoOdmBundleLoaded = \in_array(EasyAdminMongoOdmBundle::class, $container->getParameter('kernel.bundles'));
        $hasEasyAdminMongoOdmBundle = $mongoOdmBundleClassExists && $mongoOdmBundleLoaded;
        if ($hasEasyAdminMongoOdmBundle) {
            $this->coverNamespace($twigLoaderFilesystemDefinition, 'EasyAdminMongoOdm', EasyAdminMongoOdmBundle::class);
        }
    }

    private function coverNamespace(Definition $twigLoaderFilesystemDefinition, string $namespace, string $bundleClass)
    {
        $easyAdminExtensionBundleRefl = new \ReflectionClass(EasyAdminExtensionBundle::class);
        if ($easyAdminExtensionBundleRefl->isUserDefined()) {
            $easyAdminExtensionBundlePath = \dirname((string) $easyAdminExtensionBundleRefl->getFileName());
            $easyAdminExtensionTwigPath = $easyAdminExtensionBundlePath.'/Resources/views';
            $twigLoaderFilesystemDefinition->addMethodCall(
                'prependPath',
                [$easyAdminExtensionTwigPath, $namespace]
            );
        }

        $coveredBundleRefl = new \ReflectionClass($bundleClass);
        if ($coveredBundleRefl->isUserDefined()) {
            $coveredBundleBundlePath = \dirname((string) $coveredBundleRefl->getFileName());
            $coveredTwigNamespacePath = $coveredBundleBundlePath.'/Resources/views';
            // Defines a namespace from native EasyAdmin templates
            $twigLoaderFilesystemDefinition->addMethodCall(
                'addPath',
                [$coveredTwigNamespacePath, 'Base'.$namespace]
            );
        }
    }
}
