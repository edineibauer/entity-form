<?php

namespace EntityForm;

use ConnCrud\Create;
use ConnCrud\Delete;
use ConnCrud\Read;
use ConnCrud\Update;
use Entity\Entity;
use Helpers\Helper;

class Dicionario
{
    private $entity;
    private $defaultMeta;
    private $dicionario;
    private $info;
    private $relevant;
    private $metasEdited;

    /**
     * @param string $entity
     */
    public function __construct(string $entity)
    {
        $this->entity = $entity;
        $this->defaultMeta = json_decode(file_get_contents(PATH_HOME . (DEV && DOMINIO === "entity-form" ? "" : "vendor/conn/entity-form/") . "entity/input_type.json"), true);
        if ($dicEntity = Metadados::getDicionario($this->entity, true, true)) {
            foreach ($dicEntity as $i => $item) {
                if (!isset($item['id']))
                    $item['id'] = $i;
                $this->dicionario[$i] = new Meta($item, $this->defaultMeta['default']);
            }
            $this->addSelectUniqueMeta();
        } else {
            echo "Entidade não existe";
        }
    }

    /**
     * Seta valores para as Metas do Dicionário
     * valores aceitos: Array de valores, id para buscar os valores no banco, ou uma meta com valor
     * @param mixed $data
     */
    public function setData($data)
    {
        if (is_array($data) && !isset($data[0])) {
            $this->setDataArray($data);

        } elseif (is_numeric($data)) {
            $read = new Read();
            $read->exeRead($this->entity, "WHERE id = :id", "id={$data}");
            if ($read->getResult()) {
                $dataRead = $read->getResult()[0];
                foreach ($this->getAssociationMult() as $meta)
                    $dataRead[$meta->getColumn()] = $this->readMultValues($meta, $dataRead['id']);

                $this->setDataArray($dataRead);
            } else {
                $this->search(0)->setError("Id não existe");
            }

        } elseif (is_object($data) && get_class($data) === "EntityForm\Meta" && !empty($data->getValue())) {
            $this->dicionario[$data->getIndice()]->setValue($data->getValue());
            $this->metasEdited[] = $data->getColumn();
        }

        Validate::dicionario($this);
    }

    /**
     * Busca por valores multiplos
     *
     * @param Meta $m
     * @param int $id
     * @return array
     */
    private function readMultValues(Meta $m, int $id): array
    {
        $data = [];
        $read = new Read();
        $read->exeRead(PRE . $this->entity . "_" . $m->getRelation() . '_' . $m->getColumn(), "WHERE {$this->entity}_id = :id", "id={$id}");
        if ($read->getResult()) {
            foreach ($read->getResult() as $item)
                $data[] = $item[$m->getRelation() . "_id"];
        }
        return $data;
    }

    /**
     * Retorna o valor de uma meta de uma entidade
     *
     * @param string $column
     * @return mixed
     */
    public function getDataFullRead(string $column = null)
    {
        if (!empty($column))
            return (is_int($column) && isset($this->dicionario[$column]) ? $this->dicionario[$column]->getValue() : (!empty($value = $this->dicionario[$this->searchValue($column)]) ? $value->getValue() : null));

        $data = null;
        foreach ($this->dicionario as $meta) {
            if ($meta->getFormat() === "source" && preg_match('/"type": "image\//i', $meta->getValue())) {
                $data[$meta->getColumn()] = Helper::convertImageJson($meta->getValue());
            } elseif ($meta->getFormat() === "source") {
                $data[$meta->getColumn()] = json_decode($meta->getValue(), true)[0];
            } elseif (in_array($meta->getKey(), ["list", "selecao", "extend"])) {
                $data[$meta->getColumn()] = Entity::read($meta->getRelation(), $meta->getValue());
            } elseif (in_array($meta->getKey(), ["list_mult", "selecao_mult", "extend_mult"])) {
                if (!empty($meta->getValue())) {
                    $lista = [];
                    foreach (json_decode($meta->getValue(), true) as $id) {
                        $lista[] = Entity::read($meta->getRelation(), $id);
                    }
                    $data[$meta->getColumn()] = $lista;
                }
            } elseif ($meta->getType() === "json") {
                $data[$meta->getColumn()] = json_decode($meta->getValue(), true);
            } else {
                $data[$meta->getColumn()] = $meta->getValue();
            }
        }

        return $data;
    }

    /**
     * Retorna os valores do dicionárioo de forma a preencher as necessidades do Form Crud
     *
     * @param string $column
     * @return mixed
     */
    public function getDataForm()
    {
        $data = null;
        foreach ($this->dicionario as $meta)
            $data[$meta->getColumn()] = $meta->getValue();

        return $data;
    }

    /**
     * Retorna valores de uma entidade correspondente ao seu armazenamento em sql na sua tabela
     *
     * @return mixed
     */
    private function getDataOnlyEntity()
    {
        $data = null;
        foreach ($this->dicionario as $meta) {
            if (!in_array($meta->getKey(), ["extend_mult", "list_mult", "selecao_mult", "information"]) && ($meta->getFormat() !== "password" || strlen($meta->getValue()) > 3))
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
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

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

    /**
     * @return array
     */
    public function getAssociationSimple(): array
    {
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

        $data = [];
        foreach (["extend", "list", "selecao"] as $e) {
            if (!empty($this->info[$e])) {
                foreach ($this->info[$e] as $simple)
                    $data[] = $this->dicionario[$simple];
            }
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getAssociationMult(): array
    {
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

        $data = [];
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
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

        return $this->dicionario[$this->info['title']] ?? null;
    }

    /**
     * @return mixed
     */
    public function getPublisher()
    {
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

        return $this->dicionario[$this->info['publisher']] ?? null;
    }

    /**
     * @return mixed
     */
    public function getDicionario()
    {
        return $this->dicionario;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

        return $this->info;
    }

    public function getRelevant()
    {
        if (!$this->relevant)
            $this->setRelevants();

        return $this->relevant;
    }

    public function getListas()
    {
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

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
    public function search($attr, $value = null)
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
                if ($item->get($attr) === $value) {
                    $result = $item;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Deleta uma Meta do dicionário,
     * aceita indice, column ou Meta como parâmentro
     *
     * @param mixed $column
     */
    public function removeMeta($meta)
    {
        $indice = is_numeric($meta) ? ($this->dicionario[$meta] ?? null) : (is_object($meta) && get_class($meta) === "EntityForm\Meta" ? $this->searchValue($meta->getColumn()) : (is_string($meta) ? $this->searchValue($meta) : null));
        if ($indice)
            unset($this->dicionario[$indice]);
    }

    /**
     * Salva os dados do dicionário no banco de dados ou atualiza se for o caso
     */
    public function save()
    {
        $id = $this->search(0)->getValue();
        if (!$this->getError() || !empty($id))
            $this->saveAssociacaoSimples();

        if (!empty($id))
            $this->updateTableData();
        elseif (!$this->getError())
            $this->createTableData();

        if (!$this->getError() || !empty($id)) {
            if (!$this->info)
                $this->info = Metadados::getInfo($this->entity);

            $this->createRelationalData();
        }
    }

    private function updateTableData()
    {
        $id = $this->search(0)->getValue();
        if (Validate::update($this->entity, $id)) {
            $up = new Update();
            $dados = $this->getDataOnlyEntity();
            foreach ($this->dicionario as $meta) {
                if ($meta->getError() || !in_array($meta->getColumn(), $this->metasEdited))
                    unset($dados[$meta->getColumn()]);
            }

            $up->exeUpdate($this->entity, $dados, "WHERE id = :id", "id={$id}");
            if ($up->getErro())
                $this->search(0)->setError($up->getErro());
            else
                new React("update", $this->entity, $dados);
        } else {
            $this->search(0)->setValue(null, false);
        }
    }

    /**
     * @param Dicionario $d
     * @return mixed
     */
    private function createTableData()
    {
        $create = new Create();
        $dados = $this->getDataOnlyEntity();
        unset($dados['id']);
        $create->exeCreate($this->entity, $dados);
        if ($create->getErro()) {
            $this->search(0)->setError($create->getErro());
        } elseif ($create->getResult()) {
            $this->search(0)->setValue((int)$create->getResult(), false);
            $this->checkToSetOwnerList($create->getResult());
            new React("create", $this->entity, array_merge(["id" => $create->getResult()], $dados));
        }
    }

    /**
     * @param int $id
     */
    private function checkToSetOwnerList(int $id)
    {
        $general = json_decode(file_get_contents(PATH_HOME . "entity/general/general_info.json"), true);
        if(!empty($general[$this->entity]['owner'])) {
            $entityRelation = $general[$this->entity]['owner'][0];
            $column = $general[$this->entity]['owner'][1];
            $userColumn = $general[$this->entity]['owner'][2];

            $read = new Read();
            $read->exeRead($entityRelation, "WHERE {$userColumn} = :user", "user={$_SESSION['userlogin']['id']}");
            if($read->getResult()) {
                $idUser = $read->getResult()[0]['id'];

                $tableRelational = PRE . $entityRelation . "_" . $this->entity . "_" . $column;
                $read->exeRead($tableRelational, "WHERE {$entityRelation}_id = :ai && {$this->entity}_id = :bi", "ai={$idUser}&bi={$id}");
                if (!$read->getResult()) {

                    $create = new Create();
                    $create->exeCreate($tableRelational, [$entityRelation . "_id" => $idUser, $this->entity . "_id" => $id]);
                }
            }
        }
    }

    private function saveAssociacaoSimples()
    {
        if (!$this->info)
            $this->info = Metadados::getInfo($this->entity);

        foreach (["extend", "list", "selecao"] as $e) {
            if (!empty($this->info[$e])) {
                foreach ($this->info[$e] as $simple) {
                    $d = $this->dicionario[$simple]->getValue();
                    if (is_object($d) && get_class($d) === "EntityForm\Dicionario") {
                        if($e === "extend" || !empty($d->search(0)->getValue()))
                            $d->save();

                        if (!empty($d->getError()))
                            $this->dicionario[$simple]->setError($d->getError()[$d->getEntity()]);

                        if (!empty($d->search(0)->getValue()))
                            $this->dicionario[$simple]->setValue((int)$d->search(0)->getValue(), false);
                    }
                }
            }
        }
    }

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
            $this->metasEdited[] = $column;
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

    private function addSelectUniqueMeta()
    {
        foreach ($this->dicionario as $meta) {
            if (!empty($meta->getSelect())) {
                foreach ($meta->getSelect() as $select) {
                    $d = new Dicionario($meta->getRelation());
                    if ($rr = $d->search($select))
                        $this->dicionario[] = new Meta(array_replace_recursive($this->defaultMeta['default'], $this->defaultMeta['list'], ["relation" => $rr->getRelation(), "column" => $select . "__" . $meta->getColumn(), "nome" => ucwords($select)]), $this->defaultMeta['default']);
                }
            }
        }
    }

    private function setRelevants()
    {
        foreach (Metadados::getRelevantAll($this->entity) as $item) {
            if ($m = $this->search("format", $item)) {
                $this->relevant = $m;
                break;
            }
        }
    }
}