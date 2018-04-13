<?php

namespace EntityForm;


class Meta
{
    private $allow;
    private $column;
    private $default;
    private $form;
    private $format;
    private $key;
    private $nome;
    private $relation;
    private $size;
    private $type;
    private $unique;
    private $update;

    /**
     * @param mixed $dados
     */
    public function __construct($dados = null)
    {
        $this->setDados($dados);
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
        $this->default = $default;
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
    public function getDados()
    {
        return [
            "allow" => $this->allow,
            "column" => $this->column,
            "default" => $this->default,
            "form" => $this->form,
            "format" => $this->format,
            "key" => $this->key,
            "nome" => $this->nome,
            "relation" => $this->relation,
            "size" => $this->size,
            "type" => $this->type,
            "unique" => $this->unique,
            "update" => $this->update
        ];
    }

    /**
     * @param mixed $dados
     */
    public function setDados($dados = null)
    {
        $this->clearMeta();
        if ($dados)
            $this->applyDados($dados);
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
            "nome" => "",
            "relation" => "",
            "select" => [],
            "size" => false,
            "type" => "varchar",
            "unique" => false,
            "update" => true,
        ];
        $this->applyDados($dados);
    }

    private function applyDados(array $dados)
    {
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
                case 'form':
                    $this->setForm($value);
                    break;
                case 'format':
                    $this->setFormat($value);
                    break;
                case 'key':
                    $this->setKey($value);
                    break;
                case 'nome':
                    $this->setNome($value);
                    break;
                case 'relation':
                    $this->setRelation($value);
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
            }
        }
    }
}