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
        if ($this->entity) {
            $json = $this->data;
            $this->data = json_decode(file_get_contents(PATH_HOME . "vendor/conn/entity-form/entity/cache/{$this->entity}.json"), true);

            if ($this->columnChanged) {
                $this->checkChanges($json);
            }

            if ($this->columnDeleted) {
                $this->removeColumnsToEntity($json);
            }

            if ($this->columnAdded) {
                $this->addColumnsToEntity($json);
            }
        }
    }

    private function checkChanges($json)
    {
        $sql = new SqlCommand();
        foreach ($this->columnChanged as $itemm) {
            foreach ($json as $ent) {
                if ($ent['column'] === $itemm && $ent['type'] !== $this->data[$itemm]['type']) {

                    if (!$this->columnDeleted || ($this->columnDeleted && !in_array($itemm, $this->columnDeleted))) {
                        $this->columnDeleted[] = $itemm;
                    }
                    if (!$this->columnAdded || ($this->columnAdded && !in_array($itemm, $this->columnAdded))) {
                        $this->columnAdded[] = $itemm;
                    }

                } elseif($ent['column'] === $itemm && $ent['type'] === $this->data[$itemm]['type']) {

                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " MODIFY " . $this->prepareDataConfig($itemm));
                }
            }
        }
    }

    private function removeColumnsToEntity($json)
    {
        $sql = new SqlCommand();

        foreach ($this->columnDeleted as $itemd) {
            foreach ($json as $j) {
                if ($j['column'] === $itemd) {
                    $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " DROP COLUMN " . $itemd);
                }
            }
        }
    }

    private function addColumnsToEntity($json)
    {
        $sql = new SqlCommand();
        foreach ($this->columnAdded as $itema) {
            $unico = true;

            foreach ($json as $j) {
                if ($j['column'] === $itema && (!$this->columnDeleted || ($this->columnDeleted && !in_array($itema, $this->columnDeleted)))) {
                    $unico = false;
                }
            }

            if ($unico) {
                foreach ($this->data as $dd) {
                    if ($dd['column'] === $itema) {
                        $sql->exeCommand("ALTER TABLE " . PRE . $this->entity . " ADD " . $this->prepareDataConfig($itema));
                    }
                }
            }
        }
    }

    private function prepareDataConfig($item)
    {
        var_dump($this->data[$item]);
        return $item . " " . $this->data[$item]['type'] . " " . (isset($this->data[$item]['size']) ? "({$this->data[$item]['size']}) " : " ")
            . (isset($this->data[$item]['null']) && !$this->data[$item]['null'] ? "NOT NULL " : "")
            . (isset($this->data[$item]['default']) ? $this->prepareDefault($this->data[$item]['default']) : (!isset($this->data[$item]['null']) || $this->data[$item]['null'] ? "DEFAULT NULL" : ""));

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
