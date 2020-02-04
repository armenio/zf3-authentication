<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio
 */

namespace Armenio\Authentication\Adapter\DbTable;

use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter as ZendCallbackCheckAdapter;
use Zend\Authentication\Adapter\DbTable\Exception;
use Zend\Db\Sql\Select;

/**
 * Class CallbackCheckAdapter
 * @package Armenio\Authentication\Adapter\DbTable
 */
class CallbackCheckAdapter extends ZendCallbackCheckAdapter
{
    /**
     * @var bool
     */
    protected $checkIsActive = true;

    /**
     * @var array
     */
    protected $joinTables = [];

    /**
     * @param $checkIsActive
     * @return $this
     */
    public function setCheckIsActive($checkIsActive)
    {
        $this->checkIsActive = $checkIsActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCheckIsActive()
    {
        return $this->checkIsActive;
    }

    /**
     * @param $joinTables
     * @return $this
     */
    public function setJoinTables($joinTables)
    {
        if (!empty($joinTables)) {
            foreach ($joinTables as $joinTable) {
                $this->addJoinTable($joinTable);
            }
        }

        return $this;
    }

    /**
     * @param $joinTable
     * @return $this
     */
    public function addJoinTable($joinTable)
    {
        if (empty($joinTable['name'])) {
            throw new Exception\InvalidArgumentException('Invalid Join Table Name');
        }

        if (empty($joinTable['on'])) {
            $joinTable['on'] = sprintf('%s.id = %s.%s_id', $joinTable['name'], $this->tableName, $joinTable['name']);
        }

        if (empty($joinTable['columns'])) {
            $joinTable['columns'] = Select::SQL_STAR;
        }

        if (empty($joinTable['type'])) {
            $joinTable['type'] = Select::JOIN_INNER;
        }

        $this->joinTables[] = $joinTable;

        return $this;
    }

    /**
     * @return array
     */
    public function getJoinTables()
    {
        return $this->joinTables;
    }

    /**
     * @return Select
     */
    protected function authenticateCreateSelect()
    {
        // get select
        $dbSelect = parent::authenticateCreateSelect();

        $checkIsActive = $this->getCheckIsActive();
        if (true === $checkIsActive) {
            $tableName = $this->tableName;
            $dbSelect->where([sprintf('%s.active', $tableName) => 1]);
        }

        $joinTables = $this->getJoinTables();

        if (empty($joinTables)) {
            throw new Exception\RuntimeException('Empty Join Tables');
        }

        foreach ($joinTables as $joinTable) {
            if (!empty($joinTable['name'])) {
                continue;
            }

            $dbSelect->join($joinTable['name'], $joinTable['on'], $joinTable['columns'], $joinTable['type']);

            if (true === $this->getCheckIsActive()) {
                $dbSelect->where([sprintf('%s.active', $joinTable['name']) => 1]);
            }
        }

        return $dbSelect;
    }
}