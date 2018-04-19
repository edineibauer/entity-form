<?php

namespace EntityForm;

use ConnCrud\Create;
use ConnCrud\Delete;
use ConnCrud\Read;
use ConnCrud\Update;

class Dicionario
{
    private $entity;
    private $dicionario;
    private $info;
    private $relevant;

    /**
     * @param string $entity
     */
    public function __construct(string $entity)
    {
        $this->entity = $entity;
        $this->dicionario = Metadados::getDicionario($this->entity, true);
        foreach ($this->dicionario as $i => $item)
            $this->dicionario[$i] = new Meta($item, $i);
        $this->checkSelectUnique();
        $this->info = Metadados::getInfo($this->entity);
        $this->relevant = Metadados::getRelevantAll($this->entity);
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        if (is_array($data) && !isset($data[0])) {
            $this->setDataArray($data);

        } elseif (is_numeric($data)) {
            $read = new Read();
            $read->exeRead(PRE . $this->entity, "WHERE id = :id", "id={$data}");
            if ($read->getResult())
                $this->setDataArray($read->getResult()[0]);

        } elseif (is_object($data) && get_class($data) === "EntityForm\Meta") {
            $this->dicionario[$data->getIndice()] = $data;

        }

        Validate::dicionario($this);
    }

    /**
     * Retorna o valor de uma meta de uma entidade
     *
     * @param string $column
     * @return mixed
     */
    public function getData(string $column = null)
    {
        if (!empty($column))
            return (is_int($column) && isset($this->dicionario[$column]) ? $this->dicionario[$column]->getValue() : (!empty($value = $this->dicionario[$this->searchValue($column)]) ? $value->getValue() : null));

        $data = null;
        foreach ($this->dicionario as $meta) {
            if (!in_array($meta->getKey(), ["extend_mult", "list_mult", "selecao_mult"]))
                $data[$meta->getColumn()] = $meta->getValue();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        $error = null;
        foreach ($this->dicionario as $m) {
            if (!empty($m->getError()))
                $error[$this->entity][$m->getColumn()] = $m->getError();
        }
        return $error;
    }

    public function getExtends()
    {
        return $this->info['extend'];
    }

    public function getNotAssociation()
    {
        $data = null;
        foreach ($this->dicionario as $i => $item) {
            if (!in_array($item->getKey(), ["extend", "list", "selecao", "extend_mult", "list_mult", "selecao_mult", "publisher"]))
                $data[] = $this->dicionario[$i];
        }
        return $data;
    }

    public function getAssociationSimple()
    {
        $data = null;
        foreach (["extend", "list", "selecao"] as $e) {
            if (!empty($this->info[$e])) {
                foreach ($this->info[$e] as $simple)
                    $data[] = $this->dicionario[$simple];
            }
        }
        return $data;
    }

    public function getAssociationMult()
    {
        $data = null;
        foreach (["extend_mult", "list_mult", "selecao_mult"] as $e) {
            if (!empty($this->info[$e])) {
                foreach ($this->info[$e] as $mult)
                    $data[] = $this->dicionario[$mult];
            }
        }
        return $data;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->dicionario[$this->info['title']] ?? null;
    }

    /**
     * @return mixed
     */
    public function getPublisher()
    {
        return $this->dicionario[$this->info['publisher']] ?? null;
    }

    /**
     * @return mixed
     */
    public function getDicionario()
    {
        return $this->dicionario;
    }

    public function getRelevant()
    {
        return $this->relevant[0];
    }

    public function getListas()
    {
        $data = null;
        foreach (["extend_mult", "list", "selecao", "list_mult", "selecao_mult"] as $e) {
            if (!empty($this->info[$e])) {
                foreach ($this->info[$e] as $indice)
                    $data[] = $this->dicionario[$indice];
            }
        }
        return $data;
    }

    /**
     * @param mixed $attr
     * @param mixed $value
     * @return mixed
     */
    public function search($attr, $value = null): Meta
    {
        $result = null;

        if (!$value) {
            //busca por indice ou por coluna
            $result = is_int($attr) ? $this->dicionario[$attr] ?? null : $this->dicionario[$this->searchValue($attr)] ?? null;

            //caso não tenha encontrado busca por key format
            if (!$result) {
                foreach ($this->dicionario as $i => $item) {
                    if ($item->getKey() === $attr || $item->getFormat() === $attr) {
                        $result = $item;
                        break;
                    }
                }
            }
        } else {

            //busca específica
            foreach ($this->dicionario as $item) {
                if ($item->getDado($attr) === $value) {
                    $result = $item;
                    break;
                }
            }
        }

        return $result;
    }

    public function save()
    {
        if (!empty($this->search(0)->getValue()))
            $this->updateTableData();
        else
            $this->createTableData();

        if (!empty($this->search(0)->getValue()))
            $this->createRelationalData();
    }

    /**
     * @param Dicionario $d
     * @return mixed
     */
    private function updateTableData()
    {
        $id = $this->search(0)->getValue();
        if (Validate::update($this->entity, $id)) {
            $up = new Update();
            $dados = $this->getData();
            foreach ($this->dicionario as $meta) {
                if ($meta->getError())
                    unset($dados[$meta->getColumn()]);
            }

            $up->exeUpdate($this->entity, $dados, "WHERE id = :id", "id={$id}");
            if ($up->getErro())
                $this->search(0)->setError($up->getErro());
        } else {
            $this->search(0)->setValue(null);
        }
    }

    /**
     * @param Dicionario $d
     * @return mixed
     */
    private function createTableData()
    {
        if (!$this->getError()) {
            $create = new Create();
            $dados = $this->getData();
            unset($dados['id']);
            $create->exeCreate($this->entity, $dados);
            if ($create->getErro())
                $this->search(0)->setError($create->getErro());
            elseif ($create->getResult())
                $this->search(0)->setValue((int)$create->getResult());
        }
    }


    /**
     * @param Dicionario $d
     */
    private function createRelationalData()
    {
        $create = new Create();
        $del = new Delete();
        $id = $this->search(0)->getValue();
        if (!empty($this->getAssociationMult())) {
            foreach ($this->getAssociationMult() as $meta) {
                if (!empty($meta->getValue())) {
                    $entityRelation = PRE . $this->entity . "_" . $meta->getRelation() . "_" . $meta->getColumn();
                    $del->exeDelete($entityRelation, "WHERE {$this->entity}_id = :eid", "eid={$id}");
                    $listId = [];
                    foreach (json_decode($meta->getValue(), true) as $idRelation) {
                        if (!in_array($idRelation, $listId)) {
                            $listId[] = $idRelation;
                            $create->exeCreate($entityRelation, [$this->entity . "_id" => $id, $meta->getRelation() . "_id" => $idRelation]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $data
     */
    private function setDataArray(array $data)
    {
        foreach ($data as $column => $value) {
            if (is_numeric($column) && isset($this->dicionario[$column]))
                $this->dicionario[$column]->setValue($value);
            elseif (isset($this->dicionario[$field = $this->searchValue($column)]))
                $this->dicionario[$field]->setValue($value);
        }
    }

    private function searchValue($field, $search = null)
    {
        if (!$search) {
            $search = $field;
            $field = "column";
        }
        $results = array_keys(array_filter($this->dicionario, function ($value) use ($field, $search)
        {
            return $value->get($field) === $search;
        }));
        return (!empty($results) ? $results[0] : null);
    }

    private function checkSelectUnique()
    {
        $type = json_decode(file_get_contents(DEV && DOMINIO === "entityForm" && file_exists(PATH_HOME . "entity/input_type.json") ? PATH_HOME . "entity/input_type.json" : (file_exists(PATH_HOME . "vendor/conn/entity-form/entity/input_type.json") ? PATH_HOME . "vendor/conn/entity-form/entity/input_type.json" : null)), true);
        foreach ($this->dicionario as $meta) {
            if (!empty($meta->getSelect())) {
                foreach ($meta->getSelect() as $select) {
                    $d = new Dicionario($meta->getRelation());
                    $this->dicionario[] = new Meta(array_merge($type['default'], $type['list'], ["relation" => $d->search($select)->getRelation(), "column" => $select . "__" . $meta->getColumn(), "nome" => ucwords($select)]));
                }
            }
        }
    }
}