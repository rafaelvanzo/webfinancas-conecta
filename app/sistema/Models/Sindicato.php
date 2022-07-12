<?php

class Sindicato{

	public $id;
    public $funcionario_id;
    public $guia;
    public $dt_contribuicao;
    public $valor;
    public $sindicato;

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

            if($this->fields['valor']!='')
                $this->fields['valor'] = DataBase::valorToDouble($this->fields['valor']);

            if($this->fields['dt_contribuicao']!='')
                $this->fields['dt_contribuicao'] = DataBase::data_to_sql($this->fields['dt_contribuicao']);

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