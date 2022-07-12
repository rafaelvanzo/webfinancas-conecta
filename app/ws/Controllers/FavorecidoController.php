<?php

require_once(ROOT_MODULOS.'/favorecido/class/Favorecido.class.php');

/**
 * FavorecidoController short summary.
 *
 * FavorecidoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class FavorecidoController
{

    private $db;

    /**
     * Construtor
     * @param Database $dbConnection 
     */
    function __construct(Database $dbConnection = null){
        $this->db = $dbConnection;
    }

     /**
     * Criar favorecido
     * @param mixed $params
     */
    function Create($params){
        $favorecido = false;

        if($params['favorecido']['cpf_cnpj']!='')
            $favorecido = mysql_fetch_assoc(mysql_query('select id from favorecidos where cpf_cnpj = "'.$params['favorecido']['cpf_cnpj'].'"', $this->db->link_id));
        
        if($favorecido)
            echo Controller::array_to_json(array('status' => 2, 'msg' => 'Favorecido já cadastrado no Web Finanças.', 'favorecido_id' => (int)$favorecido['id']));
        else{
            $favorecido = new Favorecido($params['favorecido']);
            $favorecidoId = $favorecido->favorecidosIncluir($this->db);
            echo Controller::array_to_json(array('status' => 1, 'msg' => '', 'favorecido_id' => $favorecidoId['id']));
        }
    }

    /**
     * Editar favorecido
     * @param mixed $params 
     */
    function Edit($params){
        $favorecido = mysql_fetch_assoc(mysql_query('select id from favorecidos where id = '.$params['favorecido']['favorecido_id'], $this->db->link_id));
        if($favorecido){
            $favorecido = new Favorecido($params['favorecido']);
            $editar = $favorecido->favorecidosEditar($this->db, $params['favorecido']);
            if($editar['situacao']==1)
                echo Controller::array_to_json(array('status' => 1, 'msg' => ''));
            else
                echo Controller::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar favorecido.'));
        }else
            echo Controller::array_to_json(array('status' => 0, 'msg' => 'Favorecido não encontrado.'));
    }

    /**
     * Excluir favorecido
     * @param mixed $params 
     */
    function Delete($params){
        $favorecido = mysql_fetch_assoc(mysql_query('select id from favorecidos where id = '.$params['favorecido_id'], $this->db->link_id));
        if($favorecido){
            $favorecido = new Favorecido();
            $excluir = $favorecido->favorecidosExcluir($this->db, $params['favorecido_id']);
            if($excluir['situacao']==1)
                echo Controller::array_to_json(array('status' => 1, 'msg' => ''));
            else
                echo Controller::array_to_json(array('status' => 0, 'msg' => $excluir['notificacao']));
        }else
            echo Controller::array_to_json(array('status' => -1, 'msg' => 'Favorecido não encontrado.'));
    }

    /**
     * Retorna um favorecido pela id especificada
     * @param mixed $params
     */
    function Get($params){
        
        
        //12.356.067/0001-42  18 dígitos

        try{
        
            if(isset($params['cpf'])){

                $cpf = self::ValidarCpf($params['cpf']);
                
                $query = mysql_query('select * from favorecidos where cpf_cnpj = "'.$cpf.'"', $this->db->link_id);

            }elseif(isset($params['cnpj'])){
                
                $cnpj = self::ValidarCnpj($params['cnpj']);

                $query = mysql_query('select * from favorecidos where cpf_cnpj = "'.$cnpj.'"', $this->db->link_id);

            }else{
                
                $query = mysql_query('select * from favorecidos where id = '.$params['favorecido_id'], $this->db->link_id);
                
            }
            
            $favorecido = mysql_fetch_assoc($query);

            if($favorecido)
                echo Controller::array_to_json(array('status' => 1, 'msg' => '', 'favorecido' => $favorecido));
            else
                echo Controller::array_to_json(array('status' => 0, 'msg' => 'Favorecido não encontrado.', 'cpf' => $cpf, 'cnpj' => $cnpj));
        
        }catch(Exception $e){
        
            $exception = $e->getMessage();

            if($exception == '-1')
                echo Controller::array_to_json(array('status' => 0, 'msg' => 'CPF inválido'));
            elseif($exception == '-2')
                echo Controller::array_to_json(array('status' => 0, 'msg' => 'CNPJ inválido'));

        }
        
        
    }

    /**
     * Retorna todos os favorecidos cadastrados
     */
    function GetAll($params){
        $favorecidos = array();
        $term = $params['query'];
        if($term == '')
            $query = mysql_query('select * from favorecidos', $this->db->link_id);
        else
            $query = mysql_query('select * from favorecidos where nome like "%'.$term.'%"', $this->db->link_id);
        
        while($favorecido = mysql_fetch_assoc($query)){
            array_push($favorecidos, $favorecido);
        }
        if(count($favorecidos)>0)
            echo Controller::array_to_json($favorecidos);
        else
            echo '[]';
    }

    /**
     * Verifica se CPF está no formato correto: 110.661.377-57 14 dígitos ou 11066137757 11 dígitos
     * @param mixed $cpf 
     */
    function ValidarCpf($cpf){

        $digitos = strlen($cpf);

        if($digitos == 11){

            $cpfFormatado = substr($cpf,0,3).'.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-'.substr($cpf,9,2);

            return $cpfFormatado;

        }elseif($digitos == 14){

            return $cpf;

        }else{

            throw new Exception("-1");

        }

    }

    /**
     * Verifica se CNPJ está no formato correto: 12.356.067/0001-42 18 dígitos ou 12356067011142 14 dígitos
     * @param mixed $cnpj 
     */
    function ValidarCnpj($cnpj){

        $digitos = strlen($cnpj);

        if($digitos == 14){

            $cnpjFormatado = substr($cnpj,0,2).'.'.substr($cnpj,2,3).'.'.substr($cnpj,5,3).'/'.substr($cnpj,8,4).'-'.substr($cnpj,12,2);

            return $cnpjFormatado;

        }elseif($digitos == 18){

            return $cnpj;

        }else{

            throw new Exception("-2");

        }

    }

}
