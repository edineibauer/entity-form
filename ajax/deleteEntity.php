<?php
$entityName = filter_input(INPUT_POST, 'entity', FILTER_DEFAULT);

if (file_exists(PATH_HOME . "entity/{$entityName}.json")) {

    $sql = new \ConnCrud\SqlCommand();
    $entity = new \Entity\Entity($entityName);
    $info = $entity->getJsonInfoEntity();
    $dados = $entity->getJsonStructEntity();

    if (!empty($info['extendMult'])) {
        foreach ($info['extendMult'] as $item) {
            $sql->exeCommand("DROP TABLE " . PRE . $entityName . "_" . $dados[$item]['table']);
        }
    }
    if (!empty($info['listMult'])) {
        foreach ($info['listMult'] as $item) {
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