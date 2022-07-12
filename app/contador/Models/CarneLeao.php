<?php

class CarneLeao{

	public $id;
    public $nome;
    public $email;

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

            if($this->fields['dt_cadastro']!='')
                $this->fields['dt_cadastro'] = DataBase::data_to_sql($this->fields['dt_cadastro']);

            if($this->fields['dt_visualizacao']!='')
                $this->fields['dt_visualizacao'] = DataBase::data_to_sql($this->fields['dt_visualizacao']);
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