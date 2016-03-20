<?php
/**
 * Uri template service factory test file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModuleTest\Unit\Service;

use FinalGene\UriTemplateModule\Service\UriTemplateService;
use Zend\Test\Util\ModuleLoader;

/**
 * UriTemplateServiceFactoryTest
 */
class UriTemplateServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        /* @noinspection PhpIncludeInspection */
        $moduleLoader = new ModuleLoader(require 'config/application.config.php');
        $this->serviceManager = $moduleLoader->getServiceManager();
    }

    /**
     * Get the service manager
     *
     * @return \Zend\ServiceManager\ServiceManager
     */
    protected function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateServiceFactory::createService
     * @uses \FinalGene\UriTemplateModule\Service\UriTemplateService
     * @uses \FinalGene\UriTemplateModule\Module
     */
    public function testCreateService()
    {
        $this->assertInstanceOf(
            UriTemplateService::class,
            $this->getServiceManager()->get(UriTemplateService::class)
        );
    }
}
