<?php
/**
 * Created by PhpStorm.
 * User: nenab
 * Date: 16/09/2017
 * Time: 14:15
 */

namespace EntityForm;

use Entity\Entity;
use Helpers\Helper;

class EntityUpdate
{

    private $entity;
    private $edit;
    private $data;
    private $mod;
    private $del;
    private $add;

    public function __construct(string $entity = null, $edit = "")
    {
        if ($entity) {
            $this->setEntity($entity);
        }

        $this->edit = $edit;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
        $this->start();
    }

    /**
     * @param string $entity
     */
    public function setEntity(string $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param mixed $add
     */
    public function setAdd($add = null)
    {
        if ($add) {
            $this->add = $add;
        }
    }

    /**
     * @param mixed $del
     */
    public function setDel($del = null)
    {
        if ($del) {
            $this->del = $del;
        }
    }

    /**
     * @param mixed $mod
     */
    public function setMod($mod = null)
    {
        if ($mod) {
            $this->mod = $mod;
        }
    }

    private function start()
    {
        if ($this->entity) {
            if ($this->checkAvaliableEntityName()) {
                $this->data = $this->fixDataEntity($this->data);
                $this->createEntity();
            } else {
                echo "Nome de Entidade já em uso";
            }
        } else {
            echo "Nome da entidade ausente";
        }
    }

    private function checkAvaliableEntityName()
    {
        if (empty($this->edit) && file_exists(PATH_HOME . "entity/{$this->entity}.json")) {
            return false;
        } elseif (!empty($this->edit) && $this->edit !== $this->entity && file_exists(PATH_HOME . "/entity/{$this->entity}.json")) {
            return false;
        }

        return true;
    }

    private function fixDataEntity($data)
    {
        foreach ($data as $i => $dado) {
            unset($dado['$$hashKey'], $dado['identificador']);

            foreach ($dado as $item => $value) {
                $dado[$item] = $this->fixValue($value, $item);
                if ($this->checkDefaultValues($item, $value)) {
                    unset($dado[$item]);
                }
            }

            $dados[$dado['column']] = $dado;
        }

        return $dados;
    }

    private function checkEmptyValues($item, $value)
    {
        $emptyTags = array("prefixo", "sulfixo", "size", "allow", "allowRelation", "default", "table", "col", "class", "style", "regular");

        if (in_array($item, $emptyTags) && empty($value)) {
            return true;
        }
        return false;
    }

    private function checkTrueValues($item, $value)
    {
        $trueTags = array("null", "update", "edit", "list");
        if (in_array($item, $trueTags) && ($value === true || $value === 'true')) {
            return true;
        }
        return false;
    }

    private function checkFalseValues($item, $value)
    {
        $falseTags = array("unique", "indice");
        if (in_array($item, $falseTags) && (!$value || $value === 'false')) {
            return true;
        }
        return false;
    }

    private function checkDefaultValues($item, $value)
    {
        if (!$this->checkEmptyValues($item, $value)) {
            if (!$this->checkTrueValues($item, $value)) {
                if (!$this->checkFalseValues($item, $value)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function fixValue($value, $item)
    {
        $value = (is_array($value) ? $value : ($value === "false" ? false : ($value === "true" ? true : (is_float($value) ? (float)$value : ($value == "0" || (is_numeric($value) && !preg_match('/^0\d+/i', $value)) ? (int)$value : (empty($value) ? NULL : (string)$value))))));

        if (($item === "allow" || $item === "allowRelation") && $value && !is_array($value)) {
            $dataExplode = explode(",", $value);
            $value = array();
            foreach ($dataExplode as $item) {
                $value[] = trim($item);
            }
        }

        return $value;
    }

    private function checkIfAllreadyExistEntity()
    {
        Helper::createFolderIfNoExist(PATH_HOME . "entity");

        if (file_exists(PATH_HOME . "entity/cache/{$this->entity}.json")) {

            $json = $this->convertIdentificadorToColumnDel();

            if (file_exists(PATH_HOME . "entity/cache/{$this->entity}.json")) {
                unlink(PATH_HOME . "entity/cache/{$this->entity}.json");
            }
            if (file_exists(PATH_HOME . "entity/cache/{$this->entity}_info.json")) {
                unlink(PATH_HOME . "entity/cache/{$this->entity}_info.json");
            }

            return $json;
        }

        return null;
    }

    private function createEntity()
    {
        $update = $this->checkIfAllreadyExistEntity();

        $fp = fopen(PATH_HOME . "entity/" . $this->entity . ".json", "w");
        fwrite($fp, json_encode($this->data));
        fclose($fp);

        $data = new Entity($this->entity);

        if ($update) {
            $this->convertIdentificadorToColumnAddMod($update, $data->getJsonStructEntity());

            $manageData = new EntityUpdateStorage($this->entity);
            $manageData->setData($data->getJsonStructEntity());
            $manageData->setColumnChanged($this->mod);
            $manageData->setColumnDeleted($this->del);
            $manageData->setColumnAdded($this->add);
            $manageData->exeUpdate();
        }
    }

    private function convertIdentificadorToColumnDel()
    {
        $json = json_decode(file_get_contents(PATH_HOME . "entity/cache/{$this->entity}.json"), true);

        $del = null;
        foreach ($json as $j) {
            if ($this->del && in_array($j['identificador'], $this->del)) {
                $del[$j['identificador']]['column'] = $j['column'];
                $del[$j['identificador']]['fk'] = $j['key'] ?? null;
            }
        }
        $this->del = $del;

        return $json;

    }

    private function convertIdentificadorToColumnAddMod($old, $json)
    {
        $add = null;
        $mod = null;
        foreach ($json as $j) {
            if (!$this->del || !in_array($j['column'], $this->del)) {
                if ($this->add && in_array($j['identificador'], $this->add)) {
                    $add[] = $j['column'];
                } elseif ($this->mod && in_array($j['identificador'], $this->mod)) {

                    foreach ($old as $o) {
                        if ($o['identificador'] === $j['identificador']) {
                            $mod[$o['column']] = $j['column'];
                        }
                    }
                }
            }
        }

        $this->add = $add;
        $this->mod = $mod;
    }
}