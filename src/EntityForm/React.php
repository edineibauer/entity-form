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
        $this->checkReact(PATH_HOME . "public/", $entity, $action, $dados, $dadosOld);

        foreach (Helper::listFolder(PATH_HOME . VENDOR) as $lib)
            $this->checkReact(PATH_HOME . VENDOR . "{$lib}/", $entity, $action, $dados, $dadosOld);
    }

    /**
     * Varre todos os reacts
     *
     * @param string $path
     * @param string $entity
     * @param string $acao
     * @param array $dados
     * @param array $dadosOld
     */
    private function checkReact(string $path, string $entity, string $acao, array $dados, array $dadosOld)
    {
        if (file_exists("{$path}/react/")) {
            foreach (Helper::listFolder("{$path}/react/") as $react) {
                if(preg_match('/\.json$/i', $react)) {
                    $actions = json_decode(file_get_contents("{$path}/entity/react/{$react}"), true);

                    if (is_array($actions) && !isset($actions['entity'])) {
                        foreach ($actions as $ac)
                            $this->loadReact($ac, $path, $acao, $entity, $dados, $dadosOld);

                    } else {
                        $this->loadReact($actions, $path, $acao, $entity, $dados, $dadosOld);
                    }
                }
            }
        }
    }

    /**
     * Verifica se o react dispara com as informações nela existente
     *
     * @param array $actions
     * @param string $path
     * @param string $action
     * @param string $entity
     * @param array $dados
     * @param array $dadosOld
     */
    private function loadReact(array $actions, string $path, string $action, string $entity, array $dados, array $dadosOld)
    {
        if(!empty($actions['entity']) && !empty($actions['action']) && !empty($actions['function'])) {
            if ((is_string($actions['entity']) && $actions['entity'] === $entity) || (is_array($actions['entity']) && in_array($entity, $actions['entity']))) {
                if ((is_string($actions['action']) && $actions['action'] === $action) || (is_array($actions['action']) && in_array($action, $actions['action'])))
                    include_once "{$path}/react/function/{$actions['function']}.php";
            }
        }
    }
}