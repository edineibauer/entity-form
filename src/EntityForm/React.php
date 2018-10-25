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
        /* CRUD REACT DEFAULT */
        if(file_exists("{$path}react/function/{$entity}/{$acao}.php"))
            include "{$path}react/function/{$entity}/{$acao}.php";

        /* REACT FILE DEFAULT */
        if (file_exists("{$path}react/{$entity}.json")) {
            $actions = json_decode(file_get_contents("{$path}react/{$entity}.json"), true);

            if (is_array($actions) && !isset($actions['action'])) {
                foreach ($actions as $ac)
                    $this->loadReact($ac, $path, $acao, $entity, $dados, $dadosOld);
            } else {
                $this->loadReact($actions, $path, $acao, $entity, $dados, $dadosOld);
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
    private function loadReact(array $ac, string $path, string $acao, string $entity, array $dados, array $dadosOld)
    {
        if(((is_string($ac['action']) && $ac['action'] === $acao) || (is_array($ac['action']) && in_array($acao, $ac['action']))) && !empty($ac['function'])){
            if(is_array($ac['function'])){
                foreach ($ac['function'] as $item)
                    include "{$path}react/function/{$item}.php";
            } elseif(is_string($ac['function'])) {
                include "{$path}react/function/{$ac['function']}.php";
            }
        }
    }
}