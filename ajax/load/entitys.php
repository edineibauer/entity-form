<?php

foreach (\Helpers\Helper::listFolder(PATH_HOME . 'entity/cache') as $entidade) {
    if(!preg_match('/_info.json$/', $entidade))
        $data['data'][] = str_replace('.json', '', $entidade);
}