<?php

namespace EntityForm;

use Helpers\Helper;

class React
{

    /**
     * @param string $action
     * @param string $entity
     * @param array $dados
     */
    public function __construct(string $action, string $entity, array $dados)
    {
        foreach (Helper::listFolder(PATH_HOME . "vendor/conn") as $lib) {
            if (file_exists(PATH_HOME . "vendor/conn/{$lib}/entity/react/")) {
                foreach (Helper::listFolder(PATH_HOME . "vendor/conn/{$lib}/entity/react/") as $react) {
                    $actions = json_decode(file_get_contents(PATH_HOME . "vendor/conn/{$lib}/entity/react/{$react}"), true);

                    if ($actions['entity'] === $entity && $action === $actions['action'])
                        include_once PATH_HOME . "vendor/conn/{$lib}/ajax/react/{$actions['function']}.php";
                }
            }
        }
    }
}