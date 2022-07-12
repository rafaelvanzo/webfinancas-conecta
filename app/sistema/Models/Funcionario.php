<?php

class Funcionario{

	public $id;

    //Dados Pessoais
    public $nome;
    public $nome_pai;
    public $nome_mae;
    public $dt_nasc;
    public $cidade_nasc;
    public $uf_nasc;
    public $sexo;
    public $raca;
    public $deficiente;
    public $estado_civil;
    public $instrucao;
    public $rg;
    public $rg_emissor;
    public $rg_dt_emissao;
    public $cpf;
    public $pis;
    public $pis_dt_inscricao;
    public $carteira;
    public $carteira_dt_emissao;
    public $funcao_id;
    public $status;

    //Endereço
    public $logradouro;
	public $numero;
	public $bairro;
	public $cidade;
	public $uf;
	public $cep;
	public $complemento;
    public $referencia;
    public $tel01;
    public $tel02;
    public $email01;
    public $email02;
	
    //Registro
    public $dt_exame_admissional;
    public $dt_admissao;
    public $dt_demissao;
    public $salario;
    public $tp_salario;
    public $desconto_transporte;
    public $primeiro_emprego_ano;
    public $adicional_noturno;
    public $sindicalizado;
    public $sindicato;
    public $insalubridade;

    //Fgts
    public $optante_fgts;
    public $cod_banco_fgts;

    //Observação
	public $observacao;

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

            if($this->fields['dt_nasc']!='')
                $this->fields['dt_nasc'] = DataBase::data_to_sql($this->fields['dt_nasc']);

            if($this->fields['rg_dt_emissao']!='')
                $this->fields['rg_dt_emissao'] = DataBase::data_to_sql($this->fields['rg_dt_emissao']);

            if($this->fields['pis_dt_inscricao']!='')
                $this->fields['pis_dt_inscricao'] = DataBase::data_to_sql($this->fields['pis_dt_inscricao']);

            if($this->fields['carteira_dt_emissao']!='')
                $this->fields['carteira_dt_emissao'] = DataBase::data_to_sql($this->fields['carteira_dt_emissao']);

            if($this->fields['dt_exame_admissional']!='')
                $this->fields['dt_exame_admissional'] = DataBase::data_to_sql($this->fields['dt_exame_admissional']);

            if($this->fields['dt_admissao']!='')
                $this->fields['dt_admissao'] = DataBase::data_to_sql($this->fields['dt_admissao']);

            if($this->fields['dt_demissao']!='')
                $this->fields['dt_demissao'] = DataBase::data_to_sql($this->fields['dt_demissao']);

            if($this->fields['salario']!='')
                $this->fields['salario'] = DataBase::valorToDouble($this->fields['salario']);

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