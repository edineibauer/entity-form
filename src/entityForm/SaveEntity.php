<?php

namespace EntityForm;

use Helpers\Helper;
use LinkControl\EntityCreateEntityDatabase;

class SaveEntity
{
    private $entity;
    private $data;
    private $id;

    /**
     * Nome da entidade, dicionário de dados e identificador atual
     *
     * @param string $entity
     * @param array $data
     * @param int $id
     */
    public function __construct(string $entity = null, $data = null, int $id)
    {
        $this->entity = $entity;
        $this->data = $data;
        $this->id = $id;

        $this->start();
    }

    private function start()
    {
        try {
            $data['dicionario'] = Metadados::getDicionario($this->entity);

            if ($data['dicionario']) {
                $data['info'] = Metadados::getInfo($this->entity);
            } else {
                Helper::createFolderIfNoExist(PATH_HOME . "entity");
                Helper::createFolderIfNoExist(PATH_HOME . "entity/cache");
                Helper::createFolderIfNoExist(PATH_HOME . "entity/cache/info");
            }

            $this->data["0"] = $this->generatePrimary();
            $this->createEntityJson($this->data);
            $this->createEntityJson($this->generateInfo(), "info");

            new EntityCreateEntityDatabase($this->entity, $data);

        } catch (\Exception $e) {
            echo $e->getMessage() . " #linha {$e->getLine()}";
            die;
        }
    }

    /**
     * @param array $data
     * @param mixed $dir
     */
    private function createEntityJson(array $data, $dir = null)
    {
        $fp = fopen(PATH_HOME . "entity/cache/" . ($dir ? $dir . "/" : "") . $this->entity . ".json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
    }

    private function generatePrimary()
    {
        return [
            "format" => "none",
            "type" => "int",
            "nome" => "id",
            "column" => "id",
            "size" => "",
            "key" => "identifier",
            "unique" => "true",
            "default" => "false",
            "update" => "false",
            "relation" => "",
            "allow" => [
                "regex" => "",
                "values" => "",
                "names" => "",
                "validate" => ""
            ],
            "form" => [
                "input" => "hidden",
                "cols" => "12",
                "colm" => "",
                "coll" => "",
                "class" => "",
                "style" => ""
            ]
        ];
    }

    /**
     * @return array
     */
    private function generateInfo(): array
    {
        $data = [
            "identifier" => $this->id, "title" => null, "link" => null, "status" => null, "date" => null, "datetime" => null, "valor" => null, "email" => null, "tel" => null, "cpf" => null, "cnpj" => null, "cep" => null, "time" => null, "week" => null, "month" => null, "year" => null,
            "required" => null, "unique" => null, "constant" => null, "extend" => null, "extend_mult" => null, "list" => null, "list_mult" => null,
            "source" => [
                "image" => null,
                "audio" => null,
                "video" => null,
                "multimidia" => null,
                "compact" => null,
                "document" => null,
                "denveloper" => null,
                "arquivo" => null,
                "source" => null
            ]
        ];

        foreach ($this->data as $i => $dados) {
            if (in_array($dados['key'], ["unique", "extend", "extend_mult", "list", "list_mult"]))
                $data[$dados['key']][] = $i;

            if (in_array($dados['key'], ["title", "link", "status", "date", "datetime", "valor", "email", "tel", "cpf", "cnpj", "cep", "time", "week", "month", "year"]))
                $data[$dados['key']] = $i;

            if ($dados['key'] === "source" || $dados['key'] === "sources")
                $data['source'] = [$this->checkSource($dados['allow']['values']) => $i];

            if ($dados['default'] === false)
                $data['required'][] = $i;

            if (!$dados['update'])
                $data["constant"][] = $i;
        }

        return $data;
    }

    private function checkSource($valores)
    {
        $type = [];
        $data = [
            "image" => ["png", "jpg", "jpeg", "gif", "bmp", "tif", "tiff", "psd", "svg"],
            "video" => ["mp4", "avi", "mkv", "mpeg", "flv", "wmv", "mov", "rmvb", "vob", "3gp", "mpg"],
            "audio" => ["mp3", "aac", "ogg", "wma", "mid", "alac", "flac", "wav", "pcm", "aiff", "ac3"],
            "document" => ["txt", "doc", "docx", "dot", "dotx", "dotm", "ppt", "pptx", "pps", "potm", "potx", "pdf", "xls", "xlsx", "xltx", "rtf"],
            "compact" => ["rar", "zip", "tar", "7z"],
            "denveloper" => ["html", "css", "scss", "js", "tpl", "json", "xml", "md", "sql", "dll"]
        ];

        foreach ($data as $tipo => $dados) {
            if (count(array_intersect($dados, $valores)) > 0)
                $type[] = $tipo;
        }

        if (count($type) > 1) {
            if (count(array_intersect(["document", "compact", "denveloper"], $type)) === 0 && count(array_intersect(["image", "video", "audio"], $type)) > 1)
                return "multimidia";
            else if (count(array_intersect(["document", "compact", "denveloper"], $type)) > 1 && count(array_intersect(["image", "video", "audio"], $type)) === 0)
                return "arquivo";
            else
                return "source";
        } else {
            return $type[0];
        }
    }
}