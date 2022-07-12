<?php

class Documento{

	public $id;
    public $lancamento_id;
    public $nome_arquivo;
    public $nome_arquivo_org;
    public $tp_documento_id;
    public $classificacao_id;
    public $dt_competencia;
    public $dt_cadastro;
    public $dt_visualizacao;
    public $visualizado;

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

            if($this->fields['nome_arquivo']!='')
                $this->fields['nome_arquivo'] = self::removerAcento($this->fields['nome_arquivo']);

            if($this->fields['dt_competencia']!='')
                $this->fields['dt_competencia'] = DataBase::data_to_sql('01/'.$this->fields['dt_competencia']);

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

    /**
     * Remover acentuação
     * @param mixed $str 
     * @return string
     */
    function removerAcento($str){

	    $arrConv = array(
	    "ç" => "c",
	    "Ç" => "C",
	    "ã" => "a",
	    "Ã" => "A",
	    "á" => "a",
	    "Á" => "A",
	    "à" => "a",
	    "À" => "A",
	    "â" => "a",
	    "Â" => "A",
	    "é" => "e",
	    "É" => "E",
	    "ê" => "e",
	    "Ê" => "E",
	    "è" => "e",
	    "È" => "E",
	    "í" => "i",
	    "Í" => "I",
	    "î" => "i",
	    "Î" => "I",
	    "ì" => "i",
	    "Ì" => "I",
	    "ó" => "o",
	    "Ó" => "O",
	    "ô" => "o",
	    "Ô" => "O",
	    "õ" => "o",
	    "Õ" => "O",
	    "ò" => "o",
	    "Ò" => "O",
	    "ú" => "u",
	    "Ú" => "U",
	    "ù" => "u",
	    "Ù" => "U",
	    "ü" => "u",
	    "Ü" => "U",
	    "ñ" => "n",
	    "Ñ" => "N",
	    "ý" => "y",
	    "Ý" => "Y",
	    "\"" => "",
	    "'" => "",
	    "," => "",
	    //" " => "",
	    "<" => "",
	    ">" => "",
	    );
	    return strtr($str, $arrConv);

    }
}

class AgendaEnvio{

    public $id;
    public $tipo_documento_id;
    public $departamento_id;
    public $dt_referencia;
    public $dt_liberacao;
    public $enviar_para_todos;
    public $cliente_id;
    public $nome_cliente;
    public $dominio;
    public $proprietario;
    public $status;

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

            if($this->fields['dt_referencia']!='')
                $this->fields['dt_referencia'] = DataBase::data_to_sql($this->fields['dt_referencia']);

            if($this->fields['dt_liberacao']!='')
                $this->fields['dt_liberacao'] = DataBase::data_to_sql($this->fields['dt_liberacao']);

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