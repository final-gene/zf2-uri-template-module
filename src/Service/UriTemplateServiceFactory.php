<?php
/**
 * Uri template service factory file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModule\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UriTemplateService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $linkBuilder = new UriTemplateService();

        $linkBuilder->setRouter($serviceLocator->get('Router'));

        if (isset($serviceLocator->get('Config')['zf-rest'])) {
            $linkBuilder->setZfRestConfig($serviceLocator->get('Config')['zf-rest']);
        }

        return $linkBuilder;
    }
}
