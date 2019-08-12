<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'Wassenger',
    'description' => 'Wassenger integration',
    'author'      => 'mtcextendee.com',
    'version'     => '1.0.0',
    'services' => [
        'events'  => [],
        'forms'   => [
        ],
        'helpers' => [],
        'other'   => [
            'mautic.sms.transport.wassenger' => [
                'class'     => \MauticPlugin\MauticWassengerBundle\Wassenger\WassengerApi::class,
                'arguments' => [
                    'mautic.page.model.trackable',
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                ],
                'alias' => 'mautic.sms.config.transport.wassenger',
                'tag'          => 'mautic.sms_transport',
                'tagArguments' => [
                    'integrationAlias' => 'Wassenger',
                ],
            ],
        ],
        'models'       => [],
        'integrations' => [
            'mautic.integration.wassenger' => [
                'class' => \MauticPlugin\MauticWassengerBundle\Integration\WassengerIntegration::class,
            ],
        ],
    ],
    'routes'     => [],
    'menu'       => [],
    'parameters' => [
    ],
];
