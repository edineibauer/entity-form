<?php
$entityName = filter_input(INPUT_POST, 'entity', FILTER_DEFAULT);

if (file_exists(PATH_HOME . "entity/{$entityName}.json")) {

    $sql = new \ConnCrud\SqlCommand();
    $entity = new \Entity\Entity($entityName);
    $info = $entity->getJsonInfoEntity();
    $dados = $entity->getJsonStructEntity();

    if (!empty($info['extend_mult'])) {
        foreach ($info['extend_mult'] as $item) {
            $sql->exeCommand("DROP TABLE " . PRE . $entityName . "_" . $dados[$item]['table']);
        }
    }
    if (!empty($info['list_mult'])) {
        foreach ($info['list_mult'] as $item) {
            $sql->exeCommand("DROP TABLE " . PRE . $entityName . "_" . $dados[$item]['table']);
        }
    }

    $sql->exeCommand("DROP TABLE " . PRE . $entityName);

    unlink(PATH_HOME . "entity/{$entityName}.json");
    unlink(PATH_HOME . "entity/cache/{$entityName}.json");
    unlink(PATH_HOME . "entity/cache/{$entityName}_info.json");

} else {
    echo "Entidade não encontrada";
}