<?php

/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Authentication\Storage;

use Zend\Authentication\Storage\Session as VendorSession;

/**
 * Class Session
 *
 * @package Armenio\Authentication\Storage
 */
class Session extends VendorSession
{
    /**
     * @return \Zend\Session\ManagerInterface
     */
    public function getManager()
    {
        return $this->session->getManager();
    }
}
