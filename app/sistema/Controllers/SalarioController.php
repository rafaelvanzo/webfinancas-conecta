<?php

require_once(ROOT_SISTEMA.'Models/Salario.php');

/**
 * SalarioController short summary.
 *
 * SalarioController description.
 *
 * @version 1.0
 * @author Fabio
 */
class SalarioController
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
        $salario = mysql_fetch_assoc(mysql_query('select * from func_salarios where id = "'.$params['id'].'"', $this->db->link_id));

        if($salario['dt_alteracao']!='')
            $salario['dt_alteracao'] = DataBase::sql_to_data($salario['dt_alteracao']);

        echo Util::array_to_json($salario);
    }

    /**
     * Criar salário
     * @param mixed $params
     */
    function Create($params){
        $salario = new Salario($params);

        $salarioId = $this->db->query_insert('func_salarios', $salario->fields);
            
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Salario cadastrado com sucesso', 'salario_id' => $salarioId));
    }

    /**
     * Editar salário
     * @param mixed $params 
     */
    function Edit($params){
        
        $salario = mysql_fetch_assoc(mysql_query('select id from func_salarios where id = '.$params['id'], $this->db->link_id));
        
        if($salario){

            $salario = new Salario($params);

            $this->db->query_update('func_salarios', $salario->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
                echo Util::array_to_json(array('status' => 1, 'msg' => 'Salario atualizado com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar salario.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Registro não encontrado.'));
    
    }

    /**
     * Excluir salario
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from func_salarios where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Salario excluído com sucesso'));
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

        //$iTotalDisplayRecords = $this->db->numRows('select id from func_salarios');
        
        //Busca lançamentos que serão exibidos
        $aaData = array();
        
        //if($sSearch==""){

            $querySalario = "select * from func_salarios";

        //}else{
            
          //  $querySalario = "select * from func_salarios where nome like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($querySalario, $this->db->link_id));

        $querySalario = mysql_query($querySalario.' order by dt_alteracao desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($salario = mysql_fetch_assoc($querySalario)){
            
            $valor = $this->db->valorFormat($salario['valor']);
            $dtAlteracao = $this->db->sql_to_data($salario['dt_alteracao']);
            $opcoes = '
                <a href="'.$salario['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-salario" data-salario-id="'.$salario['id'].'" id="link-exc-'.$salario['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$salario['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-salario" data-salario-id="'.$salario['id'].'" data-dt-alteracao="'.$dtAlteracao.'" data-valor="'.$valor.'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('dt_alteracao'=>$dtAlteracao,'valor'=>'R$ '.$valor,'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }
}
?>