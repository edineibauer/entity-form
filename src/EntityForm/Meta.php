<?php

namespace EntityForm;

use Entity\Entity;
use Helpers\Check;

class Meta
{
    private $allow;
    private $column;
    private $default;
    private $error;
    private $filter;
    private $form;
    private $format;
    private $key;
    private $indice;
    private $nome;
    private $relation;
    private $select;
    private $size;
    private $type;
    private $unique;
    private $update;
    private $value;

    /**
     * @param mixed $dados
     * @param mixed $id
     */
    public function __construct($dados = null, $id = null)
    {
        $this->setDados($dados);
        if ($id !== null)
            $this->setIndice($id);
    }

    /**
     * @param mixed $allow
     */
    public function setAllow($allow = null)
    {
        $content = ['regex', 'validate', 'names', 'values'];
        if ($allow) {
            foreach ($allow as $name => $value) {
                if (in_array($name, $content))
                    $this->allow[$name] = $value;
            }
        } else {
            foreach ($content as $item)
                $this->allow[$item] = "";
        }
    }

    /**
     * @param string $column
     */
    public function setColumn(string $column)
    {
        $this->column = $column;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        if ($default === "datetime")
            $this->default = date("Y-m-d H:i:s");
        elseif ($default === "date")
            $this->default = date("Y-m-d");
        elseif ($default === "time")
            $this->default = date("H:i:s");
        else
            $this->default = $default;

    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        if (!$this->error || !$error)
            $this->error = $error;
    }

    /**
     * @param mixed $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form = null)
    {
        $content = ['input', 'cols', 'coll', 'colm', 'class', 'style'];
        if ($form) {
            foreach ($form as $name => $value) {
                if (in_array($name, $content))
                    $this->form[$name] = $value;
            }
        } else {
            foreach ($content as $item)
                $this->form[$item] = "";
        }
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param mixed $indice
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;
    }

    /**
     * @param string $nome
     */
    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    /**
     * @param string $relation
     */
    public function setRelation(string $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @param mixed $select
     */
    public function setSelect($select)
    {
        $this->select = $select;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param bool $unique
     */
    public function setUnique(bool $unique)
    {
        $this->unique = $unique;
    }

    /**
     * @param bool $update
     */
    public function setUpdate(bool $update)
    {
        $this->update = $update;
    }

    /**
     * @param mixed $value
     * @param bool $validate
     */
    public function setValue($value, bool $validate = true)
    {
        if (in_array($this->key, ["list_mult", "selecao_mult", "extend_mult"]))
            $this->checkValueExtendList($value);
        elseif (in_array($this->key, ["extend", "list", "selecao"]))
            $this->checkValueExtend($value);
        elseif ($this->key === "publisher" && !empty($_SESSION['userlogin']))
            $this->value = $value ?? $_SESSION['userlogin']['id'];
        elseif ($this->key === "publisher")
            $this->error = "Precisa estar Logado";
        else
            $this->value = $value;

        if ($validate)
            Validate::meta($this);
    }

    /**
     * @return mixed
     */
    public function getAllow()
    {
        return $this->allow;
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @return mixed
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * @return mixed
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function get($name)
    {
        return $this->$name ?? null;
    }

    /**
     * @return mixed
     */
    public function getDados()
    {
        return [
            "allow" => $this->allow,
            "column" => $this->column,
            "default" => $this->default,
            "filter" => $this->filter,
            "form" => $this->form,
            "format" => $this->format,
            "key" => $this->key,
            "indice" => $this->indice,
            "nome" => $this->nome,
            "relation" => $this->relation,
            "select" => $this->select,
            "size" => $this->size,
            "type" => $this->type,
            "unique" => $this->unique,
            "update" => $this->update,
            "value" => $this->value
        ];
    }

    /**
     * @param mixed $dados
     * @return Meta
     */
    public function setDados($dados = null): Meta
    {
        $this->clearMeta();
        if ($dados)
            $this->applyDados($dados);

        return $this;
    }

    private function clearMeta()
    {
        $dados = [
            "allow" => ['regex' => "", 'validate' => "", 'names' => [], 'values' => []],
            "column" => "",
            "default" => "",
            "filter" => [],
            "form" => ['input' => "text", 'cols' => 12, 'coll' => "", 'colm' => "", 'class' => "", 'style' => ""],
            "format" => "text",
            "key" => "",
            "indice" => "",
            "nome" => "",
            "relation" => "",
            "select" => [],
            "size" => false,
            "type" => "varchar",
            "unique" => false,
            "update" => true,
            "value" => ""
        ];
        $this->applyDados($dados);
    }

    /**
     * @param array $dados
     */
    private function applyDados(array $dados)
    {
        if (!empty($dados)) {
            foreach ($dados as $dado => $value) {
                switch ($dado) {
                    case 'allow':
                        $this->setAllow($value);
                        break;
                    case 'column':
                        $this->setColumn($value);
                        break;
                    case 'default':
                        $this->setDefault($value);
                        break;
                    case 'filter':
                        $this->setFilter($value);
                        break;
                    case 'form':
                        $this->setForm($value);
                        break;
                    case 'format':
                        $this->setFormat($value);
                        break;
                    case 'key':
                        $this->setKey($value);
                        break;
                    case 'indice':
                        $this->setIndice($value);
                        break;
                    case 'nome':
                        $this->setNome($value);
                        break;
                    case 'relation':
                        $this->setRelation($value);
                        break;
                    case 'select':
                        $this->setSelect($value);
                        break;
                    case 'size':
                        $this->setSize($value);
                        break;
                    case 'type':
                        $this->setType($value);
                        break;
                    case 'unique':
                        $this->setUnique($value);
                        break;
                    case 'value':
                        $this->setValue($value);
                        break;
                }
            }
        }
    }

    /**
     * @param mixed $value
     */
    private function checkValueExtend($value)
    {
        if (!empty($value) && (is_int($value) || (is_array($value) && !isset($value[0]))))
            $this->value = (is_int($value) ? $value : $this->getIdFromDataExtend($value));
        elseif (!empty($value))
            $this->error = "valor não esperado";
    }

    /**
     * @param mixed $value
     */
    private function checkValueExtendList($value)
    {
        if (!empty($value)) {
            if (Check::isJson($value))
                $this->checkValueExtendMult(json_decode($value, true));
            elseif (is_array($value))
                $this->checkValueExtendMult($value);
            elseif (is_int($value))
                $this->value = json_encode([0 => $value]);
            else
                $this->error = "valor não esperado para um campo do tipo {$this->key}";
        }
    }

    /**
     * @param mixed $value
     */
    private function checkValueExtendMult($value)
    {
        if (isset($value[0])) {
            if (is_numeric($value[0])) {
                $this->value = json_encode($value);
            } elseif (is_array($value[0])) {
                //dado extendido
                foreach ($value as $item) {
                    if (!isset($item[0]))
                        $this->value[] = $this->getIdFromDataExtend($item);
                    else
                        $this->error = "valor não esperado para um campo do tipo {$this->key}";
                }
                $this->value = json_encode($this->value);
            } else {
                $this->error = "valor não esperado para um campo do tipo {$this->key}";
            }
        } else {
            //dado extendido
            $this->value = json_encode([0 => $this->getIdFromDataExtend($value)]);
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    private function getIdFromDataExtend(array $data)
    {
        $return = Entity::add($this->relation, $data);
        if (!is_numeric($return)) {
            $this->error = $return[$this->relation];
            return null;
        }

        return $return;
    }
}