<?php

require_once(ROOT_SISTEMA.'Models/Sindicato.php');

/**
 * SindicatoController short summary.
 *
 * SindicatoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class SindicatoController
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
        $sindicato = mysql_fetch_assoc(mysql_query('select * from func_sindicatos where id = "'.$params['id'].'"', $this->db->link_id));

        if($sindicato['dt_contribuicao']!='')
            $sindicato['dt_contribuicao'] = DataBase::sql_to_data($sindicato['dt_contribuicao']);

        echo Util::array_to_json($sindicato);
    }

    /**
     * Criar salário
     * @param mixed $params
     */
    function Create($params){
        $sindicato = new Sindicato($params);

        $sindicatoId = $this->db->query_insert('func_sindicatos', $sindicato->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Contribuição sindical cadastrada com sucesso', 'sindicato_id' => $sindicatoId));
    }

    /**
     * Editar salário
     * @param mixed $params 
     */
    function Edit($params){
        
        $sindicato = mysql_fetch_assoc(mysql_query('select id from func_sindicatos where id = '.$params['id'], $this->db->link_id));
        
        if($sindicato){

            $sindicato = new Sindicato($params);

            $this->db->query_update('func_sindicatos', $sindicato->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
            echo Util::array_to_json(array('status' => 1, 'msg' => 'Contribuição sindical atualizada com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar sindicato.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Registro não encontrado.'));
    
    }

    /**
     * Excluir sindicato
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from func_sindicatos where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Contribuição sindical excluída com sucesso'));
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

        //$iTotalDisplayRecords = $this->db->numRows('select id from func_sindicatos');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

            $querySindicato = "select * from func_sindicatos";

        //}else{
            
          //  $querySindicato = "select * from func_sindicatos where sindicato like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($querySindicato, $this->db->link_id));

        $querySindicato = mysql_query($querySindicato.' order by dt_contribuicao desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($sindicato = mysql_fetch_assoc($querySindicato)){
            
            $valor = $this->db->valorFormat($sindicato['valor']);
            $dtContribuicao = $this->db->sql_to_data($sindicato['dt_contribuicao']);
            $opcoes = '
                <a href="'.$sindicato['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-sindicato" data-sindicato-id="'.$sindicato['id'].'" id="link-exc-'.$sindicato['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$sindicato['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-sindicato" data-sindicato-id="'.$sindicato['id'].'" data-dt-contribuicao="'.$dtContribuicao.'" data-valor="'.$valor.'" data-guia="'.$sindicato['guia'].'" data-sindicato="'.$sindicato['sindicato'].'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('guia'=>$sindicato['guia'],'dt_contribuicao'=>$dtContribuicao,'valor'=>'R$ '.$valor,'sindicato'=>$sindicato['sindicato'],'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }
}
?>