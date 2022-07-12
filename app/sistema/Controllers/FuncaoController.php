<?php

require_once(ROOT_SISTEMA.'Models/Funcao.php');

/**
 * FuncaoController short summary.
 *
 * FuncaoController description.
 *
 * @version 1.0
 * @author Fabio
 */
class FuncaoController
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
    }

    /**
     * Criar função
     * @param mixed $params
     */
    function Create($params){
        $funcao = new Funcao($params);

        $funcaoId = $this->db->query_insert('func_funcoes', $funcao->fields);
            
        return Util::array_to_json(array('status' => 1, 'msg' => 'Funcão cadastrada com sucesso', 'funcao_id' => $funcaoId));
    }

    /**
     * Editar função
     * @param mixed $params 
     */
    function Edit($params){
        
        $funcao = mysql_fetch_assoc(mysql_query('select id from func_funcoes where id = '.$params['id'], $this->db->link_id));
        
        if($funcao){

            $funcao = new Funcao($params);

            $this->db->query_update('func_funcoes', $funcao->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
            echo Util::array_to_json(array('status' => 1, 'msg' => 'Funcão atualizada com sucesso'));
            //else
              //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar funcao.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Registro não encontrado.'));
    
    }

    /**
     * Excluir função
     * @param mixed $params
     */
    function Delete($params){
        $this->db->query("delete from func_funcoes where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Funcão excluída com sucesso'));
    }

    /**
     * Criar alteração de função
     * @param mixed $params
     */
    function CreateAltFuncao($params){
        $alteracao = new AlteracaoDeFuncao($params);

        //Inclui alteração de função
        $alteracaoId = $this->db->query_insert('func_alt_funcoes', $alteracao->fields);
        
        //Atualiza função do funcionário
        $this->db->query('update funcionarios set funcao_id = '.$params['funcao_id'].' where id = '.$params['funcionario_id']);

        echo Util::array_to_json(array('status' => 1, 'msg' => 'Alteração de funcão cadastrada com sucesso', 'alteracao_id' => $alteracaoId));
    }

    /**
     * Editar alteração de função
     * @param mixed $params 
     */
    function EditAltFuncao($params){
        
        $alteracao = mysql_fetch_assoc(mysql_query('select id from func_alt_funcoes where id = '.$params['id'], $this->db->link_id));
        
        if($alteracao){

            $alteracao = new AlteracaoDeFuncao($params);

            $this->db->query_update('func_alt_funcoes', $alteracao->fields, "id = ".$params['id']);

            //if($editar['situacao']==1)
            echo Util::array_to_json(array('status' => 1, 'msg' => 'Alteração de funcão atualizada com sucesso'));
            //else
            //  echo Util::array_to_json(array('status' => 0, 'msg' => 'Erro ao atualizar funcao.'));

        }else
            echo Util::array_to_json(array('status' => 0, 'msg' => 'Registro não encontrado.'));
    }

    /**
     * Excluir alteração de função
     * @param mixed $params
     */
    function DeleteAltFuncao($params){
        $this->db->query("delete from func_alt_funcoes where id = ".$params['id']);
        echo Util::array_to_json(array('status' => 1, 'msg' => 'Alteração de funcão excluída com sucesso'));
    }

    /**
     * Summary of DataTable
     * @param mixed $db
     * @param mixed $params
     * @return string
     */
    function DataTableAltFuncao($params){

        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        //$iTotalRecords = $db->numRows('select id from lancamentos');
        $iTotalDisplayRecords = 0;

        //$iTotalDisplayRecords = $this->db->numRows('select id from func_alt_funcoes');
        
        $aaData = array();
        
        //if($sSearch==""){

            $queryFuncao = "select a.id, a.funcao_id, f.nome, a.dt_alteracao from func_alt_funcoes a join func_funcoes f on a.funcao_id = f.id";

        //}else{
            
          //  $queryFuncao = "select * from func_alt_funcoes where nome like '%".$sSearch."%'";
        //}
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryFuncao, $this->db->link_id));

        $queryFuncao = mysql_query($queryFuncao.' order by dt_alteracao desc limit '.$iDisplayStart.",".$iDisplayLength, $this->db->link_id);

        while($funcao = mysql_fetch_assoc($queryFuncao)){
            
            $dtAlteracao = $this->db->sql_to_data($funcao['dt_alteracao']);
            $opcoes = '
                <a href="'.$funcao['id'].'" title="Excluír" class="smallButton redB btTBwf tipS excluir-alt-funcao" data-funcao-id="'.$funcao['id'].'" id="link-exc-'.$funcao['id'].'"><img src="images/icons/light/close.png" width="10"></a>
                <a href="'.$funcao['id'].'" title="Editar" class="smallButton greyishB btTBwf tipS exibir-alt-funcao" data-alteracao-id="'.$funcao['id'].'" data-dt-alteracao="'.$dtAlteracao.'" data-funcao-id="'.$funcao['funcao_id'].'" data-funcao="'.$funcao['nome'].'"><img src="images/icons/light/pencil.png" width="10"></a>
            ';

            array_push($aaData,array('dt_alteracao'=>$dtAlteracao,'funcao'=>$funcao['nome'],'opcoes'=>$opcoes));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        echo json_encode($retorno);
        
    }

    /**
     * Summary of AutoCompleteFuncao
     * @param mixed $params 
     */
    function AutoCompleteFuncao($params){

        $q = $params["term"];

        if (!$q || $q==""){
            $query = mysql_query("select id, nome from func_funcoes order by nome");
        }else{
            $query = mysql_query("select id, nome from func_funcoes where nome LIKE '%".$q."%' order by nome")or die(mysql_error());
        }

        $items = array();

        while($consulta = mysql_fetch_assoc($query)){
            array_push($items, $consulta);
        }

        $this->db->close();

        if(count($items)>0){
            $result = array();
            $achou = false;
            foreach ($items as $item) {//foreach ($items as $key=>$value) {
                //if ( (strpos(strtolower($key), $q) !== false) ) {
                array_push($result, array("id"=>$item['id'], "label"=>$item['nome'], "value" => strip_tags($item['nome']), "cliente_ctr_id" => $item['cliente_ctr_id'], "fornecedor_ctr_id" => $item['fornecedor_ctr_id'], "cliente_plc_id" => $item['cliente_plc_id'], "fornecedor_plc_id" => $item['fornecedor_plc_id']));
                //}
                $keyL = strtolower($item['nome']);
                $qL = strtolower($q);
                if( $keyL == $qL )
                    $achou=true;
                //if (count($result) > 11)
                  //  break;
            }
            if($q!="" && !$achou)
                array_push($result, array("id"=>"add", "label"=>strip_tags($q)." (ADICIONAR)", "value" => strip_tags($q)));
            echo Util::array_to_json($result);
        }else{
            $result = array();
            array_push($result, array("id"=>"add", "label"=>strip_tags($q)." (ADICIONAR)", "value" => strip_tags($q)));
            echo Util::array_to_json($result);
            //echo '[]';
        }
    }

    /**
     * Incluir função via auto completar
     * @param mixed $params 
     */
    function IncluirFuncaoAc($params){
        echo self::Create($params);
    }
}
?>