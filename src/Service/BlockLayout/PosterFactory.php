<?php
namespace AgileThemeTools\Service\BlockLayout;

use AgileThemeTools\Site\BlockLayout\Poster;
use AgileThemeTools\Site\BlockLayout\RegionalHtml;
use AgileThemeTools\Site\BlockLayout\Slideshow;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PosterFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return Poster
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        return new Poster($htmlPurifier,$formElementManager);
    }
}
