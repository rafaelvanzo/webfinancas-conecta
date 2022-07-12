<?php

class Ferias{

	public $id;
    public $funcionario_id;
    public $dt_periodo_ini;
    public $dt_periodo_fim;
    public $dt_ferias_ini;
    public $dt_ferias_fim;

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

            if($this->fields['dt_periodo_ini']!='')
                $this->fields['dt_periodo_ini'] = DataBase::data_to_sql($this->fields['dt_periodo_ini']);

            if($this->fields['dt_periodo_fim']!='')
                $this->fields['dt_periodo_fim'] = DataBase::data_to_sql($this->fields['dt_periodo_fim']);

            if($this->fields['dt_ferias_ini']!='')
                $this->fields['dt_ferias_ini'] = DataBase::data_to_sql($this->fields['dt_ferias_ini']);

            if($this->fields['dt_ferias_fim']!='')
                $this->fields['dt_ferias_fim'] = DataBase::data_to_sql($this->fields['dt_ferias_fim']);

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