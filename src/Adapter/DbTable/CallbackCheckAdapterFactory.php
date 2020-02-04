<?php
/**
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Authentication\Adapter\DbTable;

use Armenio\Authentication\Adapter\DbTable\CallbackCheckAdapter as AuthAdapter;
use Armenio\Authentication\Storage\Session as AuthStorage;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter as ZendDbAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container as SessionContainer;

/**
 * Class CallbackCheckAdapterFactory
 * @package Armenio\Authentication\Adapter\DbTable
 */
class CallbackCheckAdapterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param array|null $options
     * @return object|AuthenticationService
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $session = $container->get(SessionContainer::class);
        $zendDbAdapter = $container->get(ZendDbAdapter::class);

        $config = $container->get('config');

        $tableName = isset($config['authentication']['table_name']) ? $config['authentication']['table_name'] : 'authentication';
        $identityColumn = isset($config['authentication']['identity_column']) ? $config['authentication']['identity_column'] : 'identity';
        $credentialColumn = isset($config['authentication']['credential_column']) ? $config['authentication']['credential_column'] : 'credential';

        // new adapter
        $authAdapter = new AuthAdapter($zendDbAdapter, $tableName, $identityColumn, $credentialColumn);

        $cryptCost = isset($config['authentication']['crypt_cost']) ? $config['authentication']['crypt_cost'] : 10;

        $authAdapter->setCredentialValidationCallback(function ($dbCredential, $requestCredential) use ($cryptCost) {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost($cryptCost);

            return $bcrypt->verify($requestCredential, $dbCredential);
        });

        $checkIsActive = isset($config['authentication']['check_is_active']) ? $config['authentication']['check_is_active'] : false;
        $joinTables = isset($config['authentication']['join_tables']) ? $config['authentication']['join_tables'] : [];

        $authAdapter->setCheckIsActive($checkIsActive);
        $authAdapter->setJoinTables($joinTables);

        // new storage
        $authStorage = new AuthStorage(null, null, $session->getManager());

        // start the service
        $authService = new AuthenticationService($authStorage, $authAdapter);

        return $authService;
    }
}
