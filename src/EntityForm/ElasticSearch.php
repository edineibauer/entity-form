<?php

namespace EntityForm;

use Elasticsearch\ClientBuilder;

class ElasticSearch
{
    private static $client;

    /**
     * @param Dicionario $dicionario
     */
    public static function add(Dicionario $dicionario)
    {
        $data = [];
        foreach ($dicionario as $meta)
            $data[$meta->getColumn()] = $meta->getValue();

        try {
            $client = ClientBuilder::create()->build();
            $params = [
                'index' => DATABASE,
                'type' => $dicionario->getEntity(),
                'id' => $data['id'],
                'body' => $data
            ];
            $client->index($params);
        } catch (\Exception $e) {
            var_dump($e);
        }
    }
}