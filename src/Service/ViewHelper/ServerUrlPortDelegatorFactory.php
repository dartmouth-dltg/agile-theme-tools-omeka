<?php

namespace AgileThemeTools\Service\ViewHelper;

use AgileThemeTools\View\Helper\PortAwareServerUrl;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Psr\Container\ContainerInterface;

class ServerUrlPortDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, ?array $options = null)
    {
        $realHelper = $callback();
        $config = $container->get('Config');
        $publicPort = $config['agile_theme_tools']['public_port'] ?? null;

        return new PortAwareServerUrl($realHelper, $publicPort);
    }
}
