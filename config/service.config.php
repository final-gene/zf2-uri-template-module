<?php
/**
 * Service manager config file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

namespace FinalGene\UriTemplateModule;

return [
    'service_manager' => [
        'initializers' => [
        ],
        'invokables' => [
        ],
        'factories' => [
            Service\UriTemplateService::class => Service\UriTemplateServiceFactory::class,
        ],
    ],
];
