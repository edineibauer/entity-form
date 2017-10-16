<?php

namespace EntityForm;

use ConnCrud\Delete;
use ConnCrud\Read;
use ConnCrud\TableCrud;
use Helpers\Check;

class ReadEntityForm
{
    private $entity;
    private $dados;

    public function __construct(string $entity)
    {
        $this->setEntity($entity);
        $this->dados = $this->loadEntity($entity);
        $this->dados = $this->fixValuesInfo($this->dados);
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

    private function fixValuesInfo($dados = null)
    {
        if($dados){
            foreach ($dados as $i => $dado) {
                if(!empty($dado) && isset($dado['allow']) && !empty($dado['allow']) && is_array($dado['allow'])) {

                    $dados[$i]['allow'] = implode(",", $dado['allow']);

                    if(isset($dado['allowRelation']) && !empty($dado['allowRelation']) && is_array($dado['allowRelation'])) {
                        $dados[$i]['allowRelation'] = implode(",", $dado['allowRelation']);
                    }
                }
            }
        }

        return $dados;
    }
}
