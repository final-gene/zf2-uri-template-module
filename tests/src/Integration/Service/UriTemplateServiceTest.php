<?php
/**
 * Uri template service test file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModuleTest\Integration\Service;

use FinalGene\UriTemplateModule\Service\UriTemplateService;
use Zend\Console\Console;
use Zend\Test\Util\ModuleLoader;

/**
 * UriTemplateServiceTest
 *
 * @package FinalGene\UriTemplateModuleTest\Integration\Service
 */
class UriTemplateServiceTest extends \PHPUnit_Framework_TestCase
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
        $moduleLoader = new ModuleLoader([
            'modules' => [
                'Zend\Router',
                'FinalGene\UriTemplateModuleTest\Integration\Service\UriTemplateServiceTest',
                'FinalGene\UriTemplateModule'
            ],
            'module_listener_options' => [
                'check_dependencies' => true
            ]
        ]);

        $this->serviceManager = $moduleLoader->getServiceManager();

        /* Override Console to false, otherwise ZF2 wants to load console routes */
        Console::overrideIsConsole(false);
    }

    /**
     * Test if getFromRoute returns correct templated URIs for given routes
     *
     * @dataProvider provideUriTemplatesForRoutes
     */
    public function testGetFromRoute($routeName, $expectedUriTemplate)
    {
        /** @var UriTemplateService $uriTemplateService */
        $uriTemplateService = $this->serviceManager->get(UriTemplateService::class);

        $this->assertSame($expectedUriTemplate, $uriTemplateService->getFromRoute($routeName));
    }

    /**
     * Provides routes and their expected uri templates
     *
     * @return array
     */
    public function provideUriTemplatesForRoutes()
    {
        return [
            ['phpunit/route1', '/phpunit{/foo}/bar{/baz}'],
            ['phpunit/foo/bar/baz', '/phpunit{/foo}/bar{/baz}'],
            ['phpunit/zfrest-testroutes/queryParameterTest', '/phpunit/zfrest/foo{/bar}{?queryParam1,queryParam2}'],
        ];
    }

    /**
     * Test if getFromRoute returns a correct templated URI a given route
     *
     * Test if getFromRoute returns a correct templated URI a given route after calling it two times in a row because
     * the internal state of the routes changes after the first call
     */
    public function testGetFromRouteWithoutInternalChildRoutes()
    {
        /** @var UriTemplateService $uriTemplateService */
        $uriTemplateService = $this->serviceManager->get(UriTemplateService::class);

        // we call it two times, because the intern state (childRoutes) changes after the first call
        $uriTemplateService->getFromRoute('phpunit/subroutewithoutchildroutes/testroute');

        $this->assertSame('/phpunit/subroutewithoutchildroutes/test-route-without-childroutes',
            $uriTemplateService->getFromRoute('phpunit/subroutewithoutchildroutes/testroute'));
    }

    /**
     * Test if getFromResource returns correct uri template for a given ZF-Rest resource
     */
    public function testGetFromResource()
    {
        /** @var UriTemplateService $uriTemplateService */
        $uriTemplateService = $this->serviceManager->get(UriTemplateService::class);

        $uriTemplate = $uriTemplateService->getFromResource('Phpunit\TestGetLinkForCollection\TestResource');

        $this->assertSame(
            '/phpunit/test-get-link-for-collection{/id}{?testQueryParam,justAnotherTestQueryParam}',
            $uriTemplate
        );
    }
}
