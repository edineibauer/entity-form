<?php
/**
 * Created by PhpStorm.
 * User: nenab
 * Date: 16/09/2017
 * Time: 13:50
 */

namespace EntityForm;


use Entity\Entity;
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
            $this->createKeys();
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

            foreach ($this->columnDeleted as $i => $itemd) {
                if($itemd['fk']) {
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP FOREIGN KEY " .  PRE . $itemd['column'] . "_" . $this->entity . ", DROP INDEX fk_" . $itemd['column']);
                } else {
                    $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME ='index_{$i}'");
                    if ($sql->getRowCount() > 0) {
                        $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX index_" . $i);
                    }
                }
                $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME ='unique_{$i}'");
                if ($sql->getRowCount() > 0) {
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX unique_" . $i);
                }
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP COLUMN " . $itemd['column']);
            }
        }
    }

    private function addColumnsToEntity()
    {
        if ($this->columnAdded) {
            $sql = new SqlCommand();
            foreach ($this->columnAdded as $itema) {
                $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " ADD " . $this->prepareDataConfig($itema));
                $this->checkFk($itema);
            }
        }
    }

    private function prepareDataConfig($column)
    {
        $item = $this->data[$column];
        return $column . " " . $item['type'] . " " . (isset($item['size']) && !empty($item['size']) ? "({$item['size']}) " : " ")
            . (isset($item['null']) && !$item['null'] ? "NOT NULL " : "")
            . (isset($item['default']) && !empty($item['default']) ? $this->prepareDefault($item['default']) : (!isset($item['null']) || $item['null'] ? "DEFAULT NULL" : ""));

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

    private function createKeys()
    {
        if ($this->data && is_array($this->data)) {
            $sql = new SqlCommand();
            foreach ($this->data as $column => $dados) {

                $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME = 'unique_{$dados['identificador']}'");
                if ($sql->getRowCount() > 0 && !$dados['unique']) {
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX unique_" . $dados['identificador']);
                } elseif ($sql->getRowCount() === 0 && $dados['unique']) {
                    $sql->exeCommand("ALTER TABLE `" . PRE . $this->entity . "` ADD UNIQUE KEY `unique_{$dados['identificador']}` (`{$column}`)");
                }

                $sql->exeCommand("SHOW KEYS FROM " . PRE . $this->entity . " WHERE KEY_NAME ='index_{$dados['identificador']}'");
                if ($sql->getRowCount() > 0 && !$dados['indice']) {
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP INDEX index_" . $dados['identificador']);
                } elseif ($sql->getRowCount() === 0 && $dados['indice']) {
                    $sql->exeCommand("ALTER TABLE `" . PRE . $this->entity . "` ADD KEY `index_{$dados['identificador']}` (`{$column}`)");

                }
            }
        }
    }

    private function checkFk($column)
    {
        $dados = $this->data[$column];

        if (!empty($dados['key'])) {
            if ($dados['key'] === "primary") {
                $sql = new SqlCommand();
                $sql->exeCommand("ALTER TABLE `" . PRE . $this->entity . "` ADD PRIMARY KEY (`{$column}`), MODIFY `{$column}` int(11) NOT NULL AUTO_INCREMENT");

            } elseif (in_array($dados['key'], array('extend', 'extend_mult', 'list', 'list_mult'))) {
                if (isset($dados['key_delete']) && isset($dados['key_update']) && !empty($dados['table'])) {
                    if (!$this->existEntityStorage($dados['table'])) {
                        new Entity($dados['table']);
                    }

                    if ($dados['key'] === "extend" || $dados['key'] === "list") {
                        $this->createIndexFk($this->entity, $column, 'id', $dados['table'], $dados['key_delete'], $dados['key_update']);
                    } else {
                        $this->createRelationalTable($column, $dados);
                    }
                }
            }
        }
    }

    private function createRelationalTable($column, $dados)
    {
        $table = $this->entity . "_" . $dados['table'];

        $string = "CREATE TABLE IF NOT EXISTS `" . $this->getPre($table) . "` ("
            . "`{$this->entity}_id` INT(11) NOT NULL,"
            . "`{$dados['table']}_id` INT(11) NOT NULL"
            . ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $this->exeSql($string);

        $this->createIndexFk($table, $this->entity."_id", $column, $this->entity, $dados['key_delete'], $dados['key_update']);
        $this->createIndexFk($table, $dados['table']."_id", 'id', $dados['table'], $dados['key_delete'], $dados['key_update']);
    }

    private function getPre($table)
    {
        return (defined("PRE") && !preg_match("/^" . PRE . "/i", $table) ? PRE : "") . $table;
    }

    private function createIndexFk($table, $column, $columnTarget, $tableTarget, $delete, $update)
    {
        $exe = new SqlCommand();
        $exe->exeCommand("ALTER TABLE `" . $this->getPre($table) . "` ADD KEY `fk_{$column}` (`{$column}`)");
        $exe->exeCommand("ALTER TABLE `" . $this->getPre($table) . "` ADD CONSTRAINT `" . $this->getPre($column . "_" . $table) . "` FOREIGN KEY (`{$column}`) REFERENCES `" . $this->getPre($tableTarget) . "` (`" . $columnTarget . "`) ON DELETE " . strtoupper($delete) . " ON UPDATE " . strtoupper($update));
    }

    private function existEntityStorage($entity)
    {
        $sqlTest = new SqlCommand();
        $sqlTest->exeCommand("SHOW TABLES LIKE '" . PRE . $entity . "'");

        return $sqlTest->getRowCount() > 0;
    }
}
