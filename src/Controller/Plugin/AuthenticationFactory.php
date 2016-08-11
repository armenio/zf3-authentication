<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */

namespace Armenio\Authentication\Controller\Plugin;

use Armenio\Authentication\Controller\Plugin\Authentication as AuthenticationPlugin;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class AuthenticationFactory
 * @package Armenio\Authentication\Controller\Plugin
 */
class AuthenticationFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param array|null $options
     * @return Authentication
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $helper = new AuthenticationPlugin();

        if ($container->has(AuthenticationService::class)) {
            $helper->setAuthentication($container->get(AuthenticationService::class));
        }
        return $helper;
    }
}
