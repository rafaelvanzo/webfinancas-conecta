<?php

/**
 * Honorario short summary.
 *
 * Honorario description.
 *
 * @version 1.0
 * @author Fabio
 */
class Honorario
{
    public $id;
    public $contador_id;
    public $lancamento_id;
    public $nome_contabilidade;
    public $valor;
    public $compensado;
    public $dt_vencimento;
    public $visualizado;
    public $dt_cadastro;
    public $link;

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
					$this->fields[$key] = $params[strtolower($key)];
				}
			}

            if($this->fields['dt_vencimento']!='')
                $this->fields['dt_vencimento'] = DataBase::data_to_sql($this->fields['dt_vencimento']);

            //if($this->fields['dt_cadastro']!='')
              //  $this->fields['dt_cadastro'] = DataBase::data_to_sql($this->fields['dt_cadastro']);

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
