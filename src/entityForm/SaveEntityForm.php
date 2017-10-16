<?php

namespace EntityForm;

use Entity\Metadados;
use Helpers\Helper;

class SaveEntityForm
{

    private $entity;
    private $edit;
    private $data;
    private $mod;
    private $del;
    private $add;
    private $erro;

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
            try {
                if (!$this->checkAvaliableEntityName()) {
                    throw new \Exception("Nome de Entidade já em uso");
                }

                $this->data = $this->tls($this->data);
                $this->createEntity();

            } catch (\Exception $e) {
                $this->erro = $e->getMessage() . " #linha {$e->getLine()}";
            }
        } else {
            $this->erro = "Nome da entidade ausente";
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

    private function tls($data)
    {
        $data2 = null;
        foreach ($data as $i => $dado) {
            unset($dado['$$hashKey']);

            foreach ($dado as $item => $value) {
                $dado[$item] = $this->tlsValue($value, $item);
                if ($this->valueIsSameAsDefault($item, $value)) {
                    unset($dado[$item]);
                }
            }

            $data2[$dado['column']] = $dado;
        }

        return $data2;
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

    private function valueIsSameAsDefault($item, $value)
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

    private function tlsValue($value, $item)
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

    private function createEntity()
    {
        $metadados = Metadados::getStruct($this->entity);
        if($metadados) {
            unlink(PATH_HOME . "entity/cache/{$this->entity}.json");
            unlink(PATH_HOME . "entity/cache/{$this->entity}_info.json");
        }

        $this->writeEntityForm($this->entity, $this->data);
        $metadadosCurrent = Metadados::getStruct($this->entity);

        if ($metadados) {
            $this->updateEntity($metadados, $metadadosCurrent);

        }
    }

    private function updateEntity($metadados = null, array $metadadosCurrent)
    {
        $this->del = $this->createIndexDelete($metadados);
        $this->convertIdentificadorToColumnAddMod($metadados, $metadadosCurrent);

        $manageData = new SaveDatabaseEntityForm($this->entity);
        $manageData->setData($metadadosCurrent);
        $manageData->setColumnChanged($this->mod);
        $manageData->setColumnDeleted($this->del);
        $manageData->setColumnAdded($this->add);
        $manageData->exeUpdate();
    }

    private function writeEntityForm(string $entity, $data)
    {
        $fp = fopen(PATH_HOME . "entity/" . $entity . ".json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
    }

    private function createIndexDelete(array $json)
    {
        $del = null;
        foreach ($json as $column) {
            if ($this->del && in_array($column['identificador'], $this->del)) {
                $del[$column['identificador']]['column'] = $column['column'];
                $del[$column['identificador']]['key'] = $column['key'] ?? null;
                $del[$column['identificador']]['table'] = $column['table'] ?? null;
            }
        }

        return $del;
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