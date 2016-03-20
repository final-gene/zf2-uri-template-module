<?php
/**
 * Module test file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModuleTest\Integration;

use Zend\Test\Util\ModuleLoader;

/**
 * Module test
 *
 * @package FinalGene\UriTemplateModuleTest
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The module loader
     *
     * @var ModuleLoader
     */
    protected $moduleLoader;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->moduleLoader = new ModuleLoader([
            'modules' => [
                'FinalGene\UriTemplateModule'
            ],
            'module_listener_options' => []
        ]);
    }

    /**
     * Test if the module can be loaded
     */
    public function testModuleIsLoadable()
    {
        /** @var \Zend\ModuleManager\ModuleManager $moduleManager */
        $moduleManager = $this->moduleLoader->getModuleManager();

        $this->assertNotNull(
            $moduleManager->getModule('FinalGene\UriTemplateModule'),
            'Module could not be initialized'
        );
        $this->assertInstanceOf(
            'FinalGene\UriTemplateModule\Module',
            $moduleManager->getModule('FinalGene\UriTemplateModule')
        );
    }
}
