<?php

require_once(ROOT_SISTEMA.'Models/Falta.php');

/**
 * FaltaController short summary.
 *
 * FaltaController description.
 *
 * @version 1.0
 * @author Fabio
 */
class FaltaController
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
        $falta = mysql_fetch_assoc(mysql_query('select * from funcionarios_faltas where id = "'.$params['id'].'"', $this->db->link_id));

        if($falta['dt_falta']!='')
            $falta['dt_falta'] = DataBase::sql_to_data($falta['dt_falta']);

        echo Util::array_to_json($falta);
    }

    /**
     * Criar funcionário
     * @param mixed $params
     */
    function Create($params){
        $falta = new Falta($params);

        $faltaId = $this->db->query_insert('funcionarios_faltas', $falta->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Falta cadastrada com sucesso', 'falta_id' => $faltaId));
    }

    /**
     * Editar falta
     * @param mixed $params 
     */
    function Edit($params){
        
        $falta = mysql_fetch_assoc(mysql_query('select id from funcionarios_faltas where id = '.$params['id'], $this->db->link_id));
        
        if($falta){

            $falta = new Falta($params);

            $this->db->query_update('funcionarios_faltas', $falta->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Falta atualizada com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar falta.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Falta não encontrada.'));
    
    }

    /**
     * Excluir falta
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from funcionarios_faltas where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Falta excluída com sucesso'));
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

        //$iTotalDisplayRecords = $this->db->numRows('select id from funcionarios_faltas');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

            $queryFalta = "select * from funcionarios_faltas";

        //}else{
            
          //  $queryFalta = "select * from funcionarios_faltas where nome like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryFalta, $this->db->link_id));

        $queryFalta = mysql_query($queryFalta.' order by dt_falta desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($falta = mysql_fetch_assoc($queryFalta)){
            
            $dtFalta = $this->db->sql_to_data($falta['dt_falta']);
            $justificado = ($falta['justificado']==1)? 'Sim' : 'Não';
            $opcoes = '
                <a href="'.$falta['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-falta" data-falta-id="'.$falta['id'].'" id="link-exc-'.$falta['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$falta['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-falta" data-falta-id="'.$falta['id'].'" data-dt-falta="'.$dtFalta.'" data-justificado="'.$falta['justificado'].'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('dt_falta'=>$dtFalta,'justificado'=>$justificado,'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }
}


?>