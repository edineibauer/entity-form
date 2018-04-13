<?php

namespace EntityForm;

class Dicionario
{
    private $entity;
    private $dicionario;
    private $dicionarioColumn;
    private $info;
    private $meta;
    private $relevant;


    /**
     * @param string $entity
    */
    public function __construct(string $entity)
    {
        $this->entity = $entity;
        $this->dicionario = Metadados::getDicionario($this->entity);
        $this->info = Metadados::getInfo($this->entity);
//        $this->relevant = Metadados::getRelevantAll($this->entity);
        $this->invertIndiceDicionario();
        $this->meta = new Meta();
    }

    /**
     * @return mixed
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param mixed $attr
     * @param mixed $value
     * @return mixed
     */
    public function search($attr, $value = null) :Meta
    {
        if(!$value) {
            //busca por indice ou por coluna
            $result = is_int($attr) ? $this->dicionario[$attr] ?? null : $this->dicionarioColumn[$attr] ?? null;

            //caso não tenha encontrado busca por key format
            if(!$result) {
                foreach ($this->dicionario as $i => $item) {
                    if($item['key'] === $attr || $item['format'] === $attr) {
                        $result = $item;
                        break;
                    }
                }
            }
            $this->meta->setDados($result);
        } else {

            //busca específica
            foreach ($this->dicionario as $item) {
                if($item[$attr] === $value) {
                    $this->meta->setDados($item);
                    break;
                }
            }
        }

        return $this->meta;
    }

    private function invertIndiceDicionario()
    {
        foreach ($this->dicionario as $i => $item) {
            $this->dicionarioColumn[$item['column']] = $item;
            $this->dicionarioColumn[$item['column']]['indice'] = $i;
        }
    }
}