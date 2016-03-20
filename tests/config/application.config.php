<?php
/**
 * Application config file
 *
 * @copyright Copyright (c) 2016, final gene <info@final-gene.de>
 * @author    Frank Giesecke <frank.giesecke@final-gene.de>
 */

return [
    'modules' => [
        'FinalGene\UriTemplateModule',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'check_dependencies' => true,
    ],
];
