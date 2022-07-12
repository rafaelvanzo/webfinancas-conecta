<?php

class Dependente{

	public $id;
    public $funcionario_id;
    public $nome;
    public $dt_nascimento;
    public $dt_registro;
    public $cartorio;
    public $sexo;

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

            if($this->fields['dt_nascimento']!='')
                $this->fields['dt_nascimento'] = DataBase::data_to_sql($this->fields['dt_nascimento']);

            if($this->fields['dt_registro']!='')
                $this->fields['dt_registro'] = DataBase::data_to_sql($this->fields['dt_registro']);

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


?>