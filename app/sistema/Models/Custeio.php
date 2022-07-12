<?php

/**
 * Honorario short summary.
 *
 * Honorario description.
 *
 * @version 1.0
 * @author Fabio
 */
class Custeio
{
    public $id;
    public $nome;
    public $nomeId;
    public $custoHoraDia;
    public $qtd;
    public $valor;
    public $valorComparacao;



    public $fields = array();

    /**
     * Summary of __construct
     * @param mixed $params
     */
    function __construct($params="", $bind=null){
		if($params!=""){
			
            $vars = get_class_vars(get_class($this));
			
            foreach($vars as $key => $value){
                if(array_key_exists($key,$params)){
                  $this->fields[$key] = $params[$key];
                }
             }

                if($this->fields['valor']!='')
                    $this->fields['valor'] = DataBase::valorToDouble($this->fields['valor']);

                }
            }

    /**
     * Retorna array de propriedades da classe para usar no CRUD
     * @return array
     */
    function getValues(){
		$dados = array();
		$vars = get_class_vars(get_class($this));
		foreach($vars as $key => $value){
			$dados[$key] = $this->$key;
		}
		return $dados;
	}
}
