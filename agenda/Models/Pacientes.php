<?php
/**
 * @version 1.0
 * @author Rafael Vanzo e Fabio Moreto
 * @Model
 */

/**
 * OBSERVAÇÃO
 * O C# criou os campos no banco de dados com letra inicial maiúscula, tornando necessário criar a classe com os campos da mesma forma para fazer a migração de C# para PHP.
 * array_key_exists é case sensitive e foi necessário fazer a conversão para encontrar os campos na variável $params.
 * O array $Fields é uma variável auxiliar, pois a classe Database trabalha com array.
 */

 /**
 * *** SEMPRE UTILIZAR NO MODEL AS LETRAS MINUSCULAS ***
 */

class Pacientes{
    public $Id; 
    public $cpf_cnpj;   
    public $nome;
    public $dtNascimento;
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
    public $tp_favorecido;
    public $inscricao;
    public $observacao;
    public $Fields = array();

    function __construct($params){
        $vars = get_class_vars(get_class($this));
        foreach($vars as $key => $value){
             if(array_key_exists($key,$params)){
                $this->Fields[$key] = $params[$key];
            }
        }
    }
}
?>