<?php

$entity = array();

$entidades = \Helpers\Helper::listFolder(PATH_HOME . 'entity');
foreach ($entidades as $entidade) {
    if(preg_match('/.json$/', $entidade)) {
        $entity[] = str_replace('.json', '', $entidade);
    }
}

echo json_encode($entity);