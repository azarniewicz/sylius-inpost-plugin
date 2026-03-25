<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin;

use Azarniewicz\SyliusInPostPlugin\DependencyInjection\AzarniewiczSyliusInPostPluginExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AzarniewiczSyliusInPostPlugin extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return $this->extension ??= new AzarniewiczSyliusInPostPluginExtension();
    }
}
