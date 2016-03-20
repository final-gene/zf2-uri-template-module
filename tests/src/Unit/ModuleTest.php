<?php
/**
 * Module test file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModuleTest\Unit;

use FinalGene\UriTemplateModule\Module;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;

/**
 * Class ModuleTest
 *
 * @package FinalGene\UriTemplateModuleTest\Unit
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Make sure module config can be serialized.
     *
     * Make sure module config can be serialized, because if not,
     * this breaks the application when zf2's config cache is enabled.
     *
     * @covers \FinalGene\UriTemplateModule\Module::getConfig()
     * @uses \FinalGene\UriTemplateModule\Module::loadConfig()
     */
    public function testModuleConfigIsSerializable()
    {
        $module = new Module();

        if (!$module instanceof ConfigProviderInterface) {
            $this->markTestSkipped('Module does not provide config');
        }

        $this->assertEquals($module->getConfig(), unserialize(serialize($module->getConfig())));
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Module::getModuleDependencies()
     */
    public function testModuleDependencies()
    {
        $module = new Module();

        $this->assertInstanceOf(DependencyIndicatorInterface::class, $module);

        $dependencies = $module->getModuleDependencies();

        $this->assertInternalType('array', $dependencies);
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Module::loadConfig()
     * @expectedException \InvalidArgumentException
     */
    public function testLoadConfigThrowException()
    {
        $module = new Module();

        $this->assertInstanceOf(ConfigProviderInterface::class, $module);

        $config = $this->getMethod('loadConfig');
        $config->invokeArgs($module, ['not.existing.file']);
    }

    /**
     * @covers \FinalGene\UriTemplateModule\Module::loadConfig()
     */
    public function testLoadConfigReturnConfigArray()
    {
        $module = new Module();

        $this->assertInstanceOf(ConfigProviderInterface::class, $module);

        $config = $this->getMethod('loadConfig');
        $config = $config->invokeArgs($module, ['tests/resources/Unit/ModuleTest/service.config.php']);

        $this->assertInternalType('array', $config);
    }

    /**
     * @param $name
     *
     * @return \ReflectionMethod
     */
    protected function getMethod($name)
    {
        $class = new \ReflectionClass(Module::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
