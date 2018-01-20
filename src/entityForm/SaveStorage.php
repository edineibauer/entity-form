<?php

namespace EntityForm;

class SaveStorage extends Storage
{
    private $entity;
    private $data;

    /**
     * @param string $entityName
     * @param array $dados
     */
    public function __construct(string $entityName, array $dados)
    {
        parent::__construct($entityName);
        $this->entity = $entityName;
        $this->data = Metadados::getDicionario($entityName);

        if ($this->data) {
            if ($dados['dicionario']) {
                new UpdateStorage($this->entity, $dados['dicionario']);
            } else {
                $this->prepareCommandToCreateTable();
                $this->createKeys();
            }
        }
    }

    private function prepareCommandToCreateTable()
    {
        $string = "CREATE TABLE IF NOT EXISTS `" . PRE . $this->entity . "` (`id` INT(11) NOT NULL";
        foreach ($this->data as $i => $dados) {
            if (!in_array($dados['key'], ["list_mult", "extend_mult"])) {
                $string .= ", " . parent::prepareSqlColumn($dados);
            }
        }

        $string .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";

        parent::exeSql($string);
    }

    private function createKeys()
    {
        parent::exeSql("ALTER TABLE `" . PRE . $this->entity . "` ADD PRIMARY KEY (`id`), MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");

        foreach ($this->data as $i => $dados) {
            if ($dados['unique'])
                parent::exeSql("ALTER TABLE `" . PRE . $this->entity . "` ADD UNIQUE KEY `unique_{$i}` (`{$dados['column']}`)");

            if (in_array($dados['key'], ["title", "link", "status", "email", "cpf", "cnpj", "telefone", "cep"]))
                parent::exeSql("ALTER TABLE `" . PRE . $this->entity . "` ADD KEY `index_{$i}` (`{$dados['column']}`)");

            if (in_array($dados['key'], array("extend", "extend_mult", "list", "list_mult"))) {
                if ($dados['key'] === "extend" || $dados['key'] === "list")
                    parent::createIndexFk($i, $this->entity, $dados['column'], $dados['relation'], $dados['key']);
                else
                    parent::createRelationalTable($i, $dados);
            }
        }
    }
}
