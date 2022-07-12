<?php

class Afastamento{

	public $id;
    public $funcionario_id;
    public $motivo;
    public $dt_ocorrencia;
    public $dt_alta;

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

            if($this->fields['dt_ocorrencia']!='')
                $this->fields['dt_ocorrencia'] = DataBase::data_to_sql($this->fields['dt_ocorrencia']);

            if($this->fields['dt_alta']!='')
                $this->fields['dt_alta'] = DataBase::data_to_sql($this->fields['dt_alta']);

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