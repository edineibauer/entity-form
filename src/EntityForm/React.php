<?php

namespace EntityForm;

use Helpers\Helper;

class React
{
    /**
     * React constructor.
     * @param string $action
     * @param string $entity
     * @param array $dados
     * @param array $dadosOld
     */
    public function __construct(string $action, string $entity, array $dados, array $dadosOld = [])
    {
        if(DEV)
            $this->checkReact(PATH_HOME, $entity, $action, $dados, $dadosOld);

        foreach (Helper::listFolder(PATH_HOME . "vendor/conn") as $lib)
            $this->checkReact(PATH_HOME . "vendor/conn/{$lib}/", $entity, $action, $dados, $dadosOld);
    }

    /**
     * @param string $path
     * @param string $entity
     * @param string $action
     * @param array $dados
     * @param array $dadosOld
     */
    private function checkReact(string $path, string $entity, string $action, array $dados, array $dadosOld)
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