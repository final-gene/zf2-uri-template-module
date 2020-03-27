<?php
/**
 * Uri template service factory file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModule\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

 /**
 * UriTemplateServiceFactory
 *
 * @package FinalGene\UriTemplateModule\Service
 */
class UriTemplateServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return UriTemplateService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $linkBuilder = new UriTemplateService();

        $linkBuilder->setRouter($container->get('Router'));

        if (isset($container->get('Config')['zf-rest'])) {
            $linkBuilder->setZfRestConfig($container->get('Config')['zf-rest']);
        }

        return $linkBuilder;
    }
}
