<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */

namespace Armenio\Authentication\Controller\Plugin;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Exception;

/**
 * Class Authentication
 * @package Armenio\Authentication\Controller\Plugin
 */
class Authentication extends AbstractPlugin
{
    /**
     * @var AuthenticationServiceInterface
     */
    protected $authentication;

    /**
     * @return AuthenticationServiceInterface
     */
    public function __invoke()
    {
        if (!$this->authentication instanceof AuthenticationServiceInterface) {
            throw new Exception\RuntimeException('No AuthenticationServiceInterface instance provided');
        }

        return $this->authentication;
    }

    /**
     * @param AuthenticationServiceInterface $authentication
     * @return $this
     */
    public function setAuthentication(AuthenticationServiceInterface $authentication)
    {
        $this->authentication = $authentication;
        return $this;
    }

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }
}
