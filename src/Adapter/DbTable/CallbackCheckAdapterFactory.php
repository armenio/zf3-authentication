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

        // new storage
        $authStorage = new AuthStorage(null, null, $session->getManager());

        $tableName = null;
        $identityColumn = null;
        $credentialColumn = null;
        $credentialTreatment = null;

        $cryptCost = 14;
        $checkIsActive = true;
        $joinTables = [];

        $config = $container->get('config');

        if (!empty($config['authentication'])) {

            $authentication = $config['authentication'];

            $tableName = $authentication['table_name'];
            $identityColumn = $authentication['identity_column'];
            $credentialColumn = $authentication['credential_column'];

            $cryptCost = $authentication['crypt_cost'];
            $checkIsActive = $authentication['check_is_active'];
            $joinTables = $authentication['join_tables'];
        }

        // new adapter
        $authAdapter = new AuthAdapter($zendDbAdapter, $tableName, $identityColumn, $credentialColumn);
        $authAdapter->setCredentialValidationCallback(function ($dbCredential, $requestCredential) use ($cryptCost) {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost($cryptCost);

            return $bcrypt->verify($requestCredential, $dbCredential);
        });
        $authAdapter->setCheckIsActive($checkIsActive);
        $authAdapter->setJoinTables($joinTables);

        // start the service
        $authService = new AuthenticationService($authStorage, $authAdapter);

        return $authService;
    }
}
