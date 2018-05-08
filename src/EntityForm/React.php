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
        if(DEV)
            $this->checkReact(PATH_HOME, $entity, $action, $dados);

        foreach (Helper::listFolder(PATH_HOME . "vendor/conn") as $lib)
            $this->checkReact(PATH_HOME . "vendor/conn/{$lib}/", $entity, $action, $dados);
    }

    /**
     * @param string $path
     * @param string $entity
     * @param string $action
     * @param array $dados
    */
    private function checkReact(string $path, string $entity, string $action, array $dados)
    {
        if (file_exists("{$path}/entity/react/")) {
            foreach (Helper::listFolder("{$path}/entity/react/") as $react) {
                $actions = json_decode(file_get_contents("{$path}/entity/react/{$react}"), true);

                if ($actions['entity'] === $entity && $action === $actions['action'])
                    include_once "{$path}/ajax/react/{$actions['function']}.php";
            }
        }
    }
}