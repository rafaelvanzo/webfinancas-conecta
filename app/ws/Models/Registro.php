<?php

/**
 * Registro short summary.
 *
 * Registro description.
 *
 * @version 1.0
 * @author Fabio
 */
class Cliente
{
    public $id;
    public $inscricao;
    public $cpf_cnpj;
    public $nome;
    public $email;
    public $logradouro;
    public $numero;
    public $complemento;
    public $bairro;
    public $cidade;
    public $uf;
    public $cep;
    public $telefone;
    public $celular;
    public $situacao;
    public $dt_cadastro;
    public $ws_cliente_id;
    public $fields = array();

    function __construct($params){
        $vars = get_class_vars(get_class($this));
        foreach($vars as $key => $value){
            if(array_key_exists(strtolower($key),$params) && $params[strtolower($key)] != ''){
                $this->fields[$key] = $params[strtolower($key)];
            }
        }
    }
}

class Usuario
{
    public $id;
    public $cliente_id;
    public $cliente_db_id;
    public $email;
    public $senha;
    public $situacao;
    public $dt_cadastro;
    public $financeiro;
    public $contador;
    public $grupo_id;
    public $carne_leao;
    public $fields = array();

    function __construct($params){
        $vars = get_class_vars(get_class($this));
        foreach($vars as $key => $value){
            if(array_key_exists(strtolower($key),$params) && $params[strtolower($key)] != ''){
                $this->fields[$key] = $params[strtolower($key)];
            }
        }
    }
}
