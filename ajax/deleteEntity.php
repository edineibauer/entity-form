<?php
$entityName = filter_input(INPUT_POST, 'entity', FILTER_DEFAULT);

if(file_exists(PATH_HOME . "entity/{$entityName}.json")) {
    unlink(PATH_HOME . "entity/{$entityName}.json");
    unlink(PATH_HOME . "entity/cache/{$entityName}.json");
    unlink(PATH_HOME . "entity/cache/{$entityName}_info.json");

    $sql = new \ConnCrud\SqlCommand();
    $sql->exeCommand("DROP TABLE " . PRE . $entityName);
} else {
    echo "Entidade não encontrada";
}