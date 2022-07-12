<?php

require_once(ROOT_SISTEMA.'Models/Dependente.php');

/**
 * DependenteController short summary.
 *
 * DependenteController description.
 *
 * @version 1.0
 * @author Fabio
 */
class DependenteController
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
     * Summary of Details
     * @param mixed $params 
     */
    function Details($params){
        $dependente = mysql_fetch_assoc(mysql_query('select * from func_dependentes where id = "'.$params['id'].'"', $this->db->link_id));

        if($dependente['dt_nascimento']!='')
            $dependente['dt_nascimento'] = DataBase::sql_to_data($dependente['dt_nascimento']);

        if($dependente['dt_registro']!='')
            $dependente['dt_registro'] = DataBase::sql_to_data($dependente['dt_registro']);

        echo Util::array_to_json($dependente);
    }

    /**
     * Criar salário
     * @param mixed $params
     */
    function Create($params){
        $dependente = new Dependente($params);

        $dependenteId = $this->db->query_insert('func_dependentes', $dependente->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Dependente cadastrado com sucesso', 'dependente_id' => $dependenteId));
    }

    /**
     * Editar salário
     * @param mixed $params 
     */
    function Edit($params){
        
        $dependente = mysql_fetch_assoc(mysql_query('select id from func_dependentes where id = '.$params['id'], $this->db->link_id));
        
        if($dependente){

            $dependente = new Dependente($params);

            $this->db->query_update('func_dependentes', $dependente->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Dependente atualizado com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar dependente.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Registro não encontrado.'));
    
    }

    /**
     * Excluir dependente
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from func_dependentes where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Dependente excluído com sucesso'));
    }

    /**
     * Summary of DataTable
     * @param mixed $db
     * @param mixed $params
     * @return string
     */
    function DataTable($params){

        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        //$iTotalRecords = $db->numRows('select id from lancamentos');
        $iTotalDisplayRecords = 0;

        //$iTotalDisplayRecords = $this->db->numRows('select id from func_dependentes');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

            $queryDependente = "select * from func_dependentes";

        //}else{
            
          //  $queryDependente = "select * from func_dependentes where nome like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryDependente, $this->db->link_id));

        $queryDependente = mysql_query($queryDependente.' order by nome limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($dependente = mysql_fetch_assoc($queryDependente)){
            
            $sexo = ($dependente['sexo'] == 'M')? 'Masculino' : 'Feminino';
            $dtNascimento = $this->db->sql_to_data($dependente['dt_nascimento']);
            $dtRegistro = $this->db->sql_to_data($dependente['dt_registro']);
            $opcoes = '
                <a href="'.$dependente['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-dependente" data-dependente-id="'.$dependente['id'].'" id="link-exc-'.$dependente['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$dependente['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-dependente" data-dependente-id="'.$dependente['id'].'" data-dt-nascimento="'.$dtNascimento.'" data-dt-registro="'.$dtRegistro.'" data-nome="'.$dependente['nome'].'" data-cartorio="'.$dependente['cartorio'].'" data-sexo="'.$dependente['sexo'].'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('nome'=>$dependente['nome'],'cartorio'=>$dependente['cartorio'],'sexo'=>$sexo,'dt_nascimento'=>$dtNascimento,'dt_registro'=>$dtRegistro,'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }
}
?>