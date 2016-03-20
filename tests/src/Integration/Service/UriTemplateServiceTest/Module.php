<?php
/**
 * Module test file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModuleTest\Integration\Service\UriTemplateServiceTest;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

 /**
 * Module
 *
 * @package FinalGene\UriTemplateModuleTest\Integration\Service\UriTemplateServiceTest
 */
class Module implements ConfigProviderInterface
{
    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return [
            'router' => [
                'routes' => [
                    'phpunit' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/phpunit',
                        ],
                        'child_routes' => [
                            'route1' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/:foo/bar/:baz'
                                ]
                            ],
                            'foo' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/:foo',
                                ],
                                'child_routes' => [
                                    'bar' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => '/bar',
                                        ],
                                        'child_routes' => [
                                            'baz' => [
                                                'type' => 'segment',
                                                'options' => [
                                                    'route' => '/:baz',
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'zfrest-testroutes' => [
                                'type' => 'literal',
                                'options' => [
                                    'route' => '/zfrest',
                                ],
                                'child_routes' => [
                                    'queryParameterTest' => [
                                        'type' => 'segment',
                                        'options' => [
                                            'route' => '/foo[/:bar]',
                                            'defaults' => [
                                                'controller' => 'Phpunit\ZfRestTestRoutes\QueryParameterTestResource'
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'test-get-link-for-collection' => [
                                'type' => 'segment',
                                'options' => [
                                    'route' => '/test-get-link-for-collection[/:id]',
                                    'defaults' => [
                                        'controller' => 'Phpunit\TestGetLinkForCollection\TestResource'
                                    ]
                                ]
                            ],
                            'subroutewithoutchildroutes' => [
                                'type' => 'literal',
                                'options' => [
                                    'route' => '/subroutewithoutchildroutes',
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'testroute' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => '/test-route-without-childroutes'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'zf-rest' => [
                'Phpunit\ZfRestTestRoutes\QueryParameterTestResource' => [
                    'collection_query_whitelist' => [
                        'queryParam1',
                        'queryParam2',
                    ]
                ],
                'Phpunit\TestGetLinkForCollection\TestResource' => [
                    'route_name' => 'phpunit/test-get-link-for-collection',
                    'route_identifier_name' => 'id',
                    'collection_query_whitelist' => [
                        'testQueryParam',
                        'justAnotherTestQueryParam'
                    ],
                ],
            ]
        ];
    }
}
