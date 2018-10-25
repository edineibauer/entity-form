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
        /* CRUD REACT DEFAULT */
        if(file_exists(PATH_HOME . "public/react/{$entity}/{$action}.php"))
            include PATH_HOME . "public/react/{$entity}/{$action}.php";

        foreach (Helper::listFolder(PATH_HOME . VENDOR) as $lib){
            if(file_exists(PATH_HOME . VENDOR . "{$lib}/react/{$entity}/{$action}.php"))
                include PATH_HOME . VENDOR . "{$lib}/react/{$entity}/{$action}.php";
        }
    }
}