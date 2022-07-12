<?php

require_once(ROOT_SISTEMA.'Models/Afastamento.php');

/**
 * AfastamentoController short summary.
 *
 * AfastamentoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class AfastamentoController
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
        $afastamento = mysql_fetch_assoc(mysql_query('select * from func_afastamentos where id = "'.$params['id'].'"', $this->db->link_id));

        if($afastamento['dt_ocorrencia']!='')
            $afastamento['dt_ocorrencia'] = DataBase::sql_to_data($afastamento['dt_ocorrencia']);

        if($afastamento['dt_alta']!='')
            $afastamento['dt_alta'] = DataBase::sql_to_data($afastamento['dt_alta']);

        echo Util::array_to_json($afastamento);
    }

    /**
     * Criar salário
     * @param mixed $params
     */
    function Create($params){
        $afastamento = new Afastamento($params);

        $afastamentoId = $this->db->query_insert('func_afastamentos', $afastamento->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Afastamento cadastrado com sucesso', 'afastamento_id' => $afastamentoId));
    }

    /**
     * Editar salário
     * @param mixed $params 
     */
    function Edit($params){
        
        $afastamento = mysql_fetch_assoc(mysql_query('select id from func_afastamentos where id = '.$params['id'], $this->db->link_id));
        
        if($afastamento){

            $afastamento = new Afastamento($params);

            $this->db->query_update('func_afastamentos', $afastamento->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Afastamento atualizado com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar afastamento.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Registro não encontrado.'));
    
    }

    /**
     * Excluir afastamento
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from func_afastamentos where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Afastamento excluído com sucesso'));
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

        //$iTotalDisplayRecords = $this->db->numRows('select id from func_afastamentos');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

            $queryAfastamento = "select * from func_afastamentos";

        //}else{
            
          //  $queryAfastamento = "select * from func_afastamentos where nome like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryAfastamento, $this->db->link_id));

        $queryAfastamento = mysql_query($queryAfastamento.' order by dt_ocorrencia desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($afastamento = mysql_fetch_assoc($queryAfastamento)){
            
            $dtOcorrencia = $this->db->sql_to_data($afastamento['dt_ocorrencia']);
            $dtAlta = $this->db->sql_to_data($afastamento['dt_alta']);
            $opcoes = '
                <a href="'.$afastamento['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-afastamento" data-afastamento-id="'.$afastamento['id'].'" id="link-exc-'.$afastamento['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$afastamento['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-afastamento" data-afastamento-id="'.$afastamento['id'].'" data-dt-ocorrencia="'.$dtOcorrencia.'" data-dt-alta="'.$dtAlta.'" data-motivo="'.$afastamento['motivo'].'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('motivo'=>$afastamento['motivo'],'dt_ocorrencia'=>$dtOcorrencia,'dt_alta'=>$dtAlta,'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }
}
?>