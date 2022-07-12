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

class Calendario{
    public $Id; 
    public $Situacao; 
    public $IdConsulta;   
    public $IdFavorecido;
	public $IdResponsavel;
    public $IdDoutor;
    public $DataInicial;
    public $DataFinal;
    public $TipoPlano;
    public $Observacao;
    public $Valor;
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