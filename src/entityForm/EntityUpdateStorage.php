<?php
/**
 * Created by PhpStorm.
 * User: nenab
 * Date: 16/09/2017
 * Time: 13:50
 */

namespace EntityForm;


use ConnCrud\SqlCommand;

class EntityUpdateStorage
{
    private $entity;
    private $data;
    private $columnChanged;
    private $columnDeleted;
    private $columnAdded;

    public function __construct(string $entity = null)
    {
        if ($entity) {
            $this->setEntity($entity);
        }
    }

    /**
     * @param string $entity
     */
    public function setEntity(string $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param mixed $columnAdded
     */
    public function setColumnAdded($columnAdded)
    {
        $this->columnAdded = $columnAdded;
    }

    /**
     * @param mixed $columnChanged
     */
    public function setColumnChanged($columnChanged)
    {
        $this->columnChanged = $columnChanged;
    }

    /**
     * @param mixed $columnDeleted
     */
    public function setColumnDeleted($columnDeleted)
    {
        $this->columnDeleted = $columnDeleted;
    }

    public function exeUpdate()
    {
        if ($this->entity && $this->data) {
            $this->checkChanges();
            $this->removeColumnsToEntity();
            $this->addColumnsToEntity();
        }
    }

    private function checkChanges()
    {
        if ($this->columnChanged) {
            $sql = new SqlCommand();
            foreach ($this->columnChanged as $old => $novo) {
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " CHANGE {$old} " . $this->prepareDataConfig($novo));
            }
        }
    }

    /**
     * Remove colunas que existiam
     */
    private function removeColumnsToEntity()
    {
        if ($this->columnDeleted) {
            $sql = new SqlCommand();

            foreach ($this->columnDeleted as $itemd) {
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP COLUMN " . $itemd);
            }
        }
    }

    private function addColumnsToEntity()
    {
        if ($this->columnAdded) {
            $sql = new SqlCommand();
            foreach ($this->columnAdded as $itema) {
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " ADD " . $this->prepareDataConfig($itema));
            }
        }
    }

    private function prepareDataConfig($column)
    {
        $item = $this->data[$column];
        return $column . " " . $item['type'] . " " . (isset($item['size']) ? "({$item['size']}) " : " ")
            . (isset($item['null']) && !$item['null'] ? "NOT NULL " : "")
            . (isset($item['default']) ? $this->prepareDefault($item['default']) : (!isset($item['null']) || $item['null'] ? "DEFAULT NULL" : ""));

    }

    private function prepareDefault($default)
    {
        if ($default === 'datetime' || $default === 'date' || $default === 'time') {
            return "";
        }

        if (is_numeric($default)) {
            return "DEFAULT {$default}";
        }
        return "DEFAULT '{$default}'";
    }
}
