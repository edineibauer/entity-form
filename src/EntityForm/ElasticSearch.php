<?php

namespace EntityForm;

use Elasticsearch\ClientBuilder;

class ElasticSearch
{
    private static $client;

    public function __construct()
    {
        self::$client = ClientBuilder::create()->build();
    }

    /**
     * @param Dicionario $dicionario
     */
    public static function add(Dicionario $dicionario)
    {
        $data = [];
        foreach ($dicionario as $meta)
            $data[$meta->getColumn()] = $meta->getValue();

        try {
            self::$client->index($data);
        } catch (\Exception $e) {
            var_dump($e);
        }
    }
}