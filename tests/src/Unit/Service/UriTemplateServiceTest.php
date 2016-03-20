<?php
/**
 * Entry point service test file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModuleTest\Unit\Service;

use FinalGene\UriTemplateModule\Service\UriTemplateService;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;

/**
 * Uri template service test
 *
 * @package FinalGene\UriTemplateModuleTest\Unit\Service
 */
class UriTemplateServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::setRouter
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getRouter
     */
    public function testSetAndGetRouter()
    {
        $expected = $this->getMockBuilder(SimpleRouteStack::class)
            ->getMock();
        /** @var SimpleRouteStack $expected */

        $service = new UriTemplateService();
        $service->setRouter($expected);
        $this->assertEquals($expected, $service->getRouter());
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getRouter
     * @expectedException \BadMethodCallException
     */
    public function testGetRouterWillThrowException()
    {
        $service = new UriTemplateService();
        $service->getRouter();
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::setZfRestConfig
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getZfRestConfig
     */
    public function testSetAndGetZfRestConfig()
    {
        $service = new UriTemplateService();
        $service->setZfRestConfig([]);
        $this->assertInternalType('array', $service->getZfRestConfig());
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getZfRestConfig
     * @expectedException \BadMethodCallException
     */
    public function testGetZfRestConfigWillThrowException()
    {
        $service = new UriTemplateService();
        $service->getZfRestConfig();
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getFromResource
     * @expectedException \BadMethodCallException
     */
    public function testGetFromResourceWithInvalidResourceName()
    {
        $serviceMock = $this->getMock(UriTemplateService::class, ['getZfRestConfig']);
        $serviceMock
            ->expects($this->once())
            ->method('getZfRestConfig')
            ->willReturn([]);
        /** @var UriTemplateService $serviceMock */

        $serviceMock->getFromResource('foo');
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getFromResource
     * @expectedException \BadMethodCallException
     */
    public function testGetFromResourceWithMissingRouteName()
    {
        $serviceMock = $this->getMock(UriTemplateService::class, ['getZfRestConfig']);
        $serviceMock
            ->expects($this->once())
            ->method('getZfRestConfig')
            ->willReturn([
                'foo' => [],
            ]);
        /** @var UriTemplateService $serviceMock */

        $serviceMock->getFromResource('foo');
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getFromResource
     */
    public function testGetFromResourceReturnUri()
    {
        $routeName = 'bar';
        $expectedUri = '/foo';

        $serviceMock = $this->getMock(
            UriTemplateService::class,
            [
                'getZfRestConfig',
                'getFromRoute',
            ]
        );
        $serviceMock
            ->expects($this->once())
            ->method('getZfRestConfig')
            ->willReturn([
                'foo' => [
                    'route_name' => 'bar',
                ],
            ]);
        $serviceMock
            ->expects($this->once())
            ->method('getFromRoute')
            ->with($routeName)
            ->willReturn($expectedUri);
        /** @var UriTemplateService $serviceMock */

        $this->assertEquals($expectedUri, $serviceMock->getFromResource('foo'));
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Service\UriTemplateService::getFromRoute
     */
    public function testGetFromRouteReturnUri()
    {
        $routeName = 'foo';
        $expectedUri = '/foo';

        $routerMock = $this->getMock(SimpleRouteStack::class);

        $serviceMock = $this->getMock(
            UriTemplateService::class,
            [
                'getRouter',
                'getUriTemplateForRouteAndSubroutes',
            ]
        );
        $serviceMock
            ->expects($this->once())
            ->method('getRouter')
            ->willReturn($routerMock);
        $serviceMock
            ->expects($this->once())
            ->method('getUriTemplateForRouteAndSubroutes')
            ->with($routeName, $routerMock)
            ->willReturn($expectedUri);
        /** @var UriTemplateService $serviceMock */

        $this->assertEquals($expectedUri, $serviceMock->getFromRoute($routeName));
    }
}
