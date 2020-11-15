<?php

/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Authentication\Adapter\DbTable;

use Armenio\Authentication\Storage\Session as AuthStorage;
use Interop\Container\ContainerInterface;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter as DbAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container as SessionContainer;

/**
 * Class CallbackCheckAdapterFactory
 *
 * @package Armenio\Authentication\Adapter\DbTable
 */
class CallbackCheckAdapterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param array|null $options
     *
     * @return object|AuthenticationService
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config = $container->get('config');

        return new AuthenticationService(
            new AuthStorage(
                null,
                null,
                $container->get(SessionContainer::class)->getManager()
            ),
            new AuthAdapter(
                $container->get(DbAdapter::class),
                ! empty($config['authentication']['table_name'])
                    ? $config['authentication']['table_name']
                    : 'user',
                ! empty($config['authentication']['identity_column'])
                    ? $config['authentication']['identity_column']
                    : 'identity',
                ! empty($config['authentication']['credential_column'])
                    ? $config['authentication']['credential_column']
                    : 'credential',
                function ($dbCredential, $requestCredential) use ($config) {
                    $bcrypt = new Bcrypt([
                        'cost' => ! empty($config['authentication']['crypt_cost'])
                            ? $config['authentication']['crypt_cost']
                            : 10,
                    ]);
                    return $bcrypt->verify($requestCredential, $dbCredential);
                }
            )
        );
    }
}
