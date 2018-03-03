<?php

namespace EntityForm;

use \Helpers\Helper;

class Metadados
{
    /**
     * @param string $entity
     * @param mixed $keepId
     * @return mixed
     */
    public static function getDicionario($entity, $keepId = null)
    {
        $path = PATH_HOME . "entity/cache/" . $entity . '.json';
        $data = file_exists($path) ? json_decode(file_get_contents($path), true) : null;
        if ($data) {
            if (!$keepId)
                unset($data[0]);

            return Helper::convertStringToValueArray($data);
        }

        return null;
    }

    /**
     * @param string $entity
     * @return mixed
     */
    public static function getRelevant(string $entity)
    {
        if (file_exists(PATH_HOME . "entity/relevant/{$entity}.json"))
            $relevant = json_decode(file_get_contents(PATH_HOME . PATH_HOME . "entity/relevant/{$entity}.json"), true);
        else
            $relevant = json_decode(file_get_contents(PATH_HOME . "vendor/conn/entity-form/entity/relevant.json"), true);

        $id = null;
        $info = self::getInfo($entity);
        foreach ($relevant as $r) {
            if(isset($info[$r]) && !empty($info[$r]))
                return $info[$r];
        }

        return null;
    }

    /**
     * @param string $entity
     * @return mixed
     */
    public static function getInfo($entity)
    {
        $path = PATH_HOME . "entity/cache/info/" . $entity . '.json';
        $data = file_exists($path) ? json_decode(file_get_contents($path), true) : null;
        if ($data)
            return Helper::convertStringToValueArray($data);

        return null;
    }
}