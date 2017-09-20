<?php

/**
 * <b>CreateTable:</b>
 * Obtem um arquivo JSON e o cria a tabela relacionada a ele
 *
 * @copyright (c) 2017, Edinei J. Bauer
 */

namespace EntityForm;

use ConnCrud\Delete;
use ConnCrud\Read;
use ConnCrud\TableCrud;
use Helpers\Check;

class EntityEdit
{
    private $entity;
    private $erro;
    private $dados;

    public function __construct(string $entity)
    {
        $this->setEntity($entity);
        $this->dados = $this->loadEntity($entity);
        $this->fixValuesInfo();
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getDados()
    {
        return $this->dados;
    }

    private function loadEntity($entity)
    {
        if (file_exists(PATH_HOME . 'entity/' . $entity . '.json')) {
            return json_decode(file_get_contents(PATH_HOME . 'entity/' . $entity . '.json'), true);
        }

        return array();
    }

    private function fixValuesInfo()
    {
        foreach ($this->dados as $i => $dado) {
            if(isset($dado['allow']) && !empty($dado['allow']) && is_array($dado['allow'])) {

                $this->dados[$i]['allow'] = implode(",", $dado['allow']);

                if(isset($dado['allowRelation']) && !empty($dado['allowRelation']) && is_array($dado['allowRelation'])) {
                    $this->dados[$i]['allowRelation'] = implode(",", $dado['allowRelation']);
                }
            }
        }
    }
}
