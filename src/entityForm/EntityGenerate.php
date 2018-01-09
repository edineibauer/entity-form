<?php
/**
 * Created by PhpStorm.
 * User: nenab
 * Date: 27/12/2017
 * Time: 10:59
 */

namespace EntityForm;


class EntityGenerate
{
    public function __construct(array $data)
    {
        $data = $this->performanceData($data);
        $this->createMetadados($data);
        $this->createBanco($data);
    }

    /**
     * Complementa, modifica, padroniza os dados de dicionarios recebido
     * 
     * @param array $data
     * @return array
    */
    private function performanceData(array $data) :array
    {

//        identifier -> Identificador
//        title -> Título
//        link -> Link
//        status -> Status
//        valor -> R$ Valor
//        url -> Url
//        email -> Email
//        password -> Password
//        tel -> Telefone
//        cpf -> Cpf
//        cnpj -> Cnpj
//        cep -> Cep
//        date -> Data
//        datetime -> Data & Hora
//        time -> Hora
//        week -> Semana
//        month -> Mês
//        year -> Ano

        return $data;
    }

    /**
     * Cria os dicionarios da entidade no sistema
     *
     * @param array $data
     */
    private function createMetadados(array $data)
    {
        
    }

    /**
     * Cria o banco de dados da Entidade
     *
     * @param array $data
     */
    private function createBanco(array $data)
    {
        
    }
}