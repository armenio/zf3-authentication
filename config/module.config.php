<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */

namespace Armenio\Authentication;

use Armenio\Authentication\Adapter\DbTable\CallbackCheckAdapterFactory as AuthenticationServiceFactory;
use Zend\Authentication\AuthenticationService;

return [
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => AuthenticationServiceFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'authentication' => Controller\Plugin\AuthenticationFactory::class,
        ],
    ],
];