<?php

namespace EntityForm;

use ConnCrud\Read;
use Entity\Entity;
use Helpers\Check;

class Validate
{
    /**
     * Valida valor a ser inserido na meta
     *
     * @param Meta $m
     * @param $value
     * @return mixed
     */
    public static function meta(Meta $m)
    {
        if ($m->getColumn() !== "id") {
            self::checkDefaultSet($m);
            if (!empty($m->getValue())) {
                self::convertValues($m);
                self::checkType($m);
                self::checkSize($m);
                self::checkRegular($m);
                self::checkValidate($m);
                self::checkValues($m);
            }
        }
    }

    /**
     * @param Dicionario $d
     */
    public static function dicionario(Dicionario $d)
    {
        if (Entity::checkPermission($d->getEntity(), $d->search(0)->getValue())) {
            foreach ($d->getDicionario() as $m) {
                if ($m->getColumn() !== "id") {
                    self::checkLink($d, $m);
                    self::checkUnique($d, $m);

                    if ($m->getKey() === "link" && $m->getError()) {
                        $d->getRelevant()->setError($m->getError());
                        $m->setError(null);
                        $m->setValue(null, false);
                    }

                    if ($m->getError())
                        $m->setValue(null, false);
                }
            }
        } else {
            $d->search(0)->setError("Permissão Negada");
        }
    }

    /**
     * @param string $entity
     * @param mixed $id
     * @return mixed
     */
    public static function update(string $entity, $id = null)
    {
        if (empty($id))
            return false;

        $read = new Read();
        $read->exeRead($entity, "WHERE id = :id", "id={$id}");
        return $read->getResult() ? true : false;
    }

    /**
     * Verifica se o campo é do tipo link, se for, linka o valor ao título
     *
     * @param Dicionario $d
     * @param Meta $m
     */
    private static function checkLink(Dicionario $d, Meta $m)
    {
        if ($m->getKey() === "link") {
            if (!empty($d->getRelevant()->getValue()))
                $m->setValue(Check::name($d->getRelevant()->getValue()));
        }
    }

    /**
     * Verifica se precisa alterar de modo padrão a informação deste campo
     *
     * @param Meta $m
     * @param mixed $value
     */
    protected static function checkDefaultSet(Meta $m)
    {
        if ($m->getValue() === "")
            $m->setValue(null, false);

        if ($m->getValue() === null) {
            if ($m->getDefault() === false)
                $m->setError("Preencha este Campo");
            elseif (!empty($m->getDefault()))
                $m->setValue($m->getDefault(), false);
        }
    }

    /**
     * Verifica se o tipo do campo é o desejado
     *
     * @param Meta $m
     */
    private static function convertValues(Meta $m)
    {
        if ($m->getFormat() === "password")
            $m->setValue(Check::password($m->getValue()), false);

        if ($m->getFormat() === "json" && is_array($m->getValue()))
            $m->setValue(json_encode($m->getValue()), false);

        if ($m->getKey() === "link")
            $m->setValue(Check::name($m->getValue()), false);
    }

    /**
     * Verifica se o tipo do campo é o desejado
     *
     * @param Meta $m
     */
    private static function checkType(Meta $m)
    {
        if (in_array($m->getType(), ["tinyint", "smallint", "mediumint", "int", "bigint"])) {
            if (!is_numeric($m->getValue()))
                $m->setError("número inválido");

        } elseif ($m->getType() === "decimal") {
            $size = (!empty($m->getSize()) ? explode(',', str_replace(array('(', ')'), '', $m->getSize())) : array(10, 30));
            $val = explode('.', str_replace(',', '.', $m->getValue()));
            if (strlen($val[1]) > $size[1])
                $m->setError("valor das casas decimais excedido. Max {$size[1]}");
            elseif (strlen($val[0]) > $size[0])
                $m->setError("valor inteiro do valor decimal excedido. Max {$size[0]}");

        } elseif (in_array($m->getType(), array("double", "real", "float"))) {
            if (!is_numeric($m->getValue()))
                $m->setError("valor não é um número");

        } elseif (in_array($m->getType(), array("bit", "boolean", "serial"))) {
            if (!is_bool($m->getValue()))
                $m->setError("valor boleano inválido. (true ou false)");

        } elseif (in_array($m->getType(), array("datetime", "timestamp"))) {
            if (!preg_match('/\d{4}-\d{2}-\d{2}[T\s]+\d{2}:\d{2}/i', $m->getValue()))
                $m->setError("formato de data inválido ex válido:(2017-08-23 21:58:00)");

        } elseif ($m->getType() === "date") {
            if (!preg_match('/\d{4}-\d{2}-\d{2}/i', $m->getValue()))
                $m->setError("formato de data inválido ex válido:(2017-08-23)");

        } elseif ($m->getType() === "time") {
            if (!preg_match('/\d{2}:\d{2}/i', $m->getValue()))
                $m->setError("formato de tempo inválido ex válido:(21:58)");

        } elseif ($m->getType() === "json" && !Check::isJson($m->getValue())) {
            $m->setError("formato json inválido");
        }
    }

    /**
     * Verifica se o tamanho do valor corresponde ao desejado
     *
     * @param Meta $m
     */
    private static function checkSize(Meta $m)
    {
        if ($m->getSize()) {
            $length = strlen($m->getValue());
            if ($m->getType() === "varchar" && $length > $m->getSize())
                $m->setError("tamanho máximo de caracteres excedido. Max {$m->getSize()}");

            elseif ($m->getType() === "char" && $length > 1)
                $m->setError("tamanho máximo de caracteres excedido. Max {$m->getSize()}");

            elseif ($m->getType() === "tinytext" && ($length > 255 || $length > $m->getSize()))
                $m->setError("tamanho máximo de caracteres excedido. Max {$m->getSize()}");

            elseif ($m->getType() === "text" && ($length > 65535 || $length > $m->getSize()))
                $m->setError("tamanho máximo de caracteres excedido. Max {$m->getSize()}");

            elseif ($m->getType() === "mediumtext" && ($length > 16777215 || $length > $m->getSize()))
                $m->setError("tamanho máximo de caracteres excedido. Max {$m->getSize()}");

            elseif ($m->getType() === "longtext" && ($length > 4294967295 || $length > $m->getSize()))
                $m->setError("tamanho máximo de caracteres excedido. Max {$m->getSize()}");

            elseif ($m->getType() === "tinyint" && ($m->getValue() > self::intLength($m->getSize()) || $m->getValue() > self::intLength(8)))
                $m->setError("numero excedeu seu limite. Max " . self::intLength($m->getSize()));

            elseif ($m->getType() === "smallint" && ($m->getValue() > self::intLength($m->getSize()) || $m->getValue() > self::intLength(16)))
                $m->setError("numero excedeu seu limite. Max " . self::intLength($m->getSize()));

            elseif ($m->getType() === "mediumint" && ($m->getValue() > self::intLength($m->getSize()) || $m->getValue() > self::intLength(24)))
                $m->setError("numero excedeu seu limite. Max " . self::intLength($m->getSize()));

            elseif ($m->getType() === "int" && !in_array($m->getKey(), ["extend", "list", "list_mult", "selecao", "selecao_mult", "extend_mult"]) && ($m->getValue() > self::intLength($m->getSize()) || $m->getValue() > self::intLength(32)))
                $m->setError("numero excedeu seu limite. Max " . self::intLength($m->getSize()));

            elseif ($m->getType() === "bigint" && ($m->getValue() > self::intLength($m->getSize()) || $m->getValue() > self::intLength(64)))
                $m->setError("numero excedeu seu limite. Max " . self::intLength($m->getSize()));
        }
    }

    /**
     * @param int $value
     * @return int
     */
    private static function intLength(int $value): int
    {
        return (pow(2, ($value * 2)) - 1);
    }

    /**
     * Verifica se o valor precisa ser único
     *
     * @param Dicionario $d
     * @param Meta $m
     */
    private static function checkUnique(Dicionario $d, Meta $m)
    {
        if ($m->getUnique()) {
            $read = new Read();
            $read->exeRead($d->getEntity(), "WHERE {$m->getColumn()} = '{$m->getValue()}'" . (!empty($d->search("id")->getValue()) ? " && id != " . $d->search("id")->getValue() : ""));
            if ($read->getResult())
                $m->setError("Valor já existe, informe outro");
        }
    }

    /**
     * Verifica se existe expressão regular, e se existe, aplica a verificação
     *
     * @param Meta $m
     */
    private static function checkRegular(Meta $m)
    {
        if (!empty($m->getAllow()['regex']) && is_string($m->getValue()) && !preg_match($m->getAllow()['regex'], $m->getValue()))
            $m->setError("formato não permitido.");
    }

    /**
     * Verifica se o campo precisa de validação pré-formatada
     *
     * @param Meta $m
     */
    private static function checkValidate(Meta $m)
    {
        if (!empty($m->getAllow()['validate'])) {
            if ($m->getAllow()['validate'] === "email" && !Check::email($m->getValue()))
                $m->setError("email inválido.");

            elseif ($m->getAllow()['validate'] === "cpf" && !Check::cpf($m->getValue()))
                $m->setError("CPF inválido.");

            elseif ($m->getAllow()['validate'] === "cnpj" && !Check::cnpj($m->getValue()))
                $m->setError("CNPJ inválido.");
        }
    }

    /**
     * Verifica se existem valores exatos permitidos
     *
     * @param Meta $m
     */
    private static function checkValues(Meta $m)
    {
        if ($m->getType() === "json") {
            if (!empty($m->getValue()) && !empty($m->getAllow()['values'])) {
                if (in_array($m->getFormat(), ["sources", "source"])) {
                    foreach (json_decode($m->getValue(), true) as $v) {
                        if (!in_array(pathinfo($v['url'], PATHINFO_EXTENSION), $m->getAllow()['values']))
                            $m->setError("valor não é permitido");
                    }
                } else {
                    foreach (json_decode($m->getValue(), true) as $item) {
                        if (!empty($item) && !in_array($item, $m->getAllow()['values']))
                            $m->setError("valor não é permitido");
                    }
                }
            }
        } else {
            if (!empty($m->getAllow()['values']) && !empty($m->getValue()) && !in_array($m->getValue(), $m->getAllow()['values']))
                $m->setError("valor não é permitido");
        }
    }
}