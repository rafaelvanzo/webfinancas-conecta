<?php
class Grupo{
	
	public $id;
    public $sistema_id;
    public $cliente_id;
    public $nome;
	public $fields = array();

	//CONSTRUTOR
	//================================================================================================

    function __construct($params=''){
        if($params!=''){
            $vars = get_class_vars(get_class($this));
            foreach($vars as $key => $value){
                if(array_key_exists(strtolower($key),$params) && $params[strtolower($key)] != ''){
                    $this->fields[$key] = $params[strtolower($key)];
                }
            }
            if($params['permissoes']!=''){
                $permissoes = $params['permissoes'];
                $permissoes = str_replace('\"','"',$permissoes);
                $permissoes = str_replace("\'","'",$permissoes);
                $this->fields['permissoes'] = json_decode($permissoes, true);
            }
        }
    }

    //INCLUÍR GRUPO
    //==============================================================================================

    /**
     * Summary of CreateGrupo
     * @param mixed $db 
     * @throws Exception 
     * @return string
     */
    function CreateGrupo($db){
        
        try{
            $clienteId = $_SESSION['cliente_id'];
            
            $permissoes = $this->fields['permissoes'];
            unset($this->fields['permissoes']);

            $this->fields['sistema_id'] = 1;
            $this->fields['cliente_id'] = $clienteId;
            
            //insere grupo
            if(!$grupoId = $db->query_insert('usuarios_grupos',$this->fields))
                if($db->errno==1062)
                    throw new Exception('Email já cadastrado',$db->errno);

            //insere permissões do grupo
            foreach($permissoes as $p){
                $permissao = array(
                        'cliente_id'=>$clienteId,
                        'grupo_id'=>$grupoId,
                        'permissao_id'=>$p['id']
                    );
                if(!$db->query_insert('usuarios_grupos_permissoes',$permissao))
                    throw new Exception('',$db->errno);
            }

            $grupos = $db->fetch_all_array('select id, nome from usuarios_grupos where cliente_id = '.$_SESSION['cliente_id'].' order by nome');

            return json_encode(array('status'=>1,'msg'=>'Grupo cadastrado com sucesso','grupos'=>$grupos));
        
        }catch(Exception $e){
            
            return json_encode(array('status'=>2,'msg'=>$e->getMessage(),'errno'=>$e->getCode(),'full_erro'=>$db->full_error));

        }
        
    }

    //EDITAR GRUPO
    //==============================================================================================
    
    /**
     * Summary of EditGrupo
     * @param mixed $db 
     * @throws Exception 
     * @return string
     */
    function EditGrupo($db){
        
        try{
            $clienteId = $_SESSION['cliente_id'];
            $grupoId = $this->fields['id'];

            $permissoes = $this->fields['permissoes'];
            unset($this->fields['permissoes']);

            //start: Atualiza grupo
            $db->query_update('usuarios_grupos',$this->fields,'id = '.$this->fields['id']);
            //end: Atualiza grupo
            
            //start: Atualiza roles do grupo
            $db->query('delete from usuarios_grupos_permissoes where grupo_id = '.$grupoId);
            
            foreach($permissoes as $p){
                $permissao = array(
                        'cliente_id'=>$clienteId,
                        'grupo_id'=>$grupoId,
                        'permissao_id'=>$p['id']
                    );
                if(!$db->query_insert('usuarios_grupos_permissoes',$permissao))
                    throw new Exception('',$db->errno);
            }
            //end: Atualiza roles do grupo
            
            $grupos = $db->fetch_all_array('select id, nome from usuarios_grupos where cliente_id = '.$_SESSION['cliente_id'].' order by nome');

            return json_encode(array('status'=>1,'msg'=>'Grupo atualizado com sucesso','grupos'=>$grupos));
            
        }
        catch(Exception $e){
            
            return json_encode(array('status'=>2,'msg'=>$e->getMessage(),'errno'=>$e->getCode(),'full_erro'=>$db->full_error));

        }
        
    }

    //EXCLUIR GRUPO
    //==============================================================================================

    /**
     * Summary of DeleteGrupo
     * @param mixed $db 
     * @param mixed $params 
     * @return string
     */
    function DeleteGrupo($db,$params){
        try{
            
            if(!$db->query('delete from usuarios_grupos where id = '.$params['grupoId']))
                throw new Exception('Existem usuários relacionados a este grupo.',$db->errno);
            
            return json_encode(array('status'=>1,'msg'=>'Grupo excluído com sucesso'));

        }catch(Exception $e){

            return json_encode(array('status'=>2,'msg'=>$e->getMessage(),'errno'=>$e->getCode(),'full_erro'=>$db->full_error));

        }
    }

    //EXIBIR GRUPO
    //==============================================================================================

    /**
     * Summary of DetailsGrupo
     * @param mixed $db 
     * @param mixed $params 
     * @return string
     */
    function DetailsGrupo($db,$params){
        $grupo = $db->fetch_assoc('select * from usuarios_grupos where id = '.$params['grupoId']);
        $grupo['permissoes'] = array();
        $permissoes = $db->fetch_all_array('select permissao_id from usuarios_grupos_permissoes where grupo_id = '.$params['grupoId']);
        foreach($permissoes as $p){
            array_push($grupo['permissoes'],$p['permissao_id']);
        }
        return json_encode($grupo);
    }

    //DATA TABLE GRUPOS
    //================================================================================================

    function DataTableGrupos($db,$params){

        //filtro do data table
        $sSearch = $params["sSearch"];
        $sEcho = $params["sEcho"];
        $iDisplayStart = $params["iDisplayStart"];
        $iDisplayLength = $params["iDisplayLength"];
        //$iTotalRecords = $db->numRows('select id from lancamentos');
        $iTotalDisplayRecords = 0;

        $iTotalDisplayRecords = $db->numRows('select id from usuarios_grupos');
        
        $aaData = array();
        
        if($sSearch==""){

            $queryGrupos = "select id, nome from usuarios_grupos where cliente_id = ".$_SESSION['cliente_id']." and sistema_id = 1";

        }else{
            
            $queryGrupos = "select id, nome from usuarios_grupos where cliente_id = ".$_SESSION['cliente_id']." and sistema_id = 1 and nome like '%".$sSearch."%'";
        }
		
        $iTotalDisplayRecords = mysql_num_rows(mysql_query($queryGrupos, $db->link_id));

        $queryGrupos = mysql_query($queryGrupos.' order by nome limit '.$iDisplayStart.",".$iDisplayLength, $db->link_id);

        while($grupo = mysql_fetch_assoc($queryGrupos)){

            $dadosGrupo = '
                <span class="lDespesa tbWF" >
	                <a href="javascript://void(0);" style="cursor: default;" original-title="Favorecido" class="tipS" ><strong >'.$grupo['nome'].'</strong></a>
                </span>
															
                <div class="tbWFoption">
                    <a href="'.$grupo['id'].'" original-title="Excluír" class="smallButton redB btTBwf tipS excluir-grupo" id="link-exc-'.$grupo['id'].'"><img src="images/icons/light/close.png" width="10"></a>							
                    <a href="javascript://void(0);" original-title="Editar" class="smallButton greyishB btTBwf tipS exibir-grupo" data-grupo-id='.$grupo['id'].'><img src="images/icons/light/pencil.png" width="10"></a>
                </div>
            ';

            array_push($aaData,array('grupo'=>$dadosGrupo));
        }

        $retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalDisplayRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords,'aaData'=>$aaData);
        
        return json_encode($retorno);

    }

    //RETORNAR MÓDULOS E RESPECTIVAS PERMISSÕES
    //================================================================================================

    function GetModulos($db){
        
        $arrayModulos = array();
        
        $modulos = $db->fetch_all_array('select id, nome from sis_modulos where sistema_id = 1 order by id');
        
        foreach($modulos as $modulo){
            $arrayPermissoes = array();
            $permissoes = $db->fetch_all_array('select id, nome from sis_permissoes where modulo_id = '.$modulo['id']);
            foreach($permissoes as $permissao){
                array_push($arrayPermissoes,$permissao['id']);
            }
            array_push($arrayModulos, array('modulo'=>$modulo['nome'],'permissoes'=>$arrayPermissoes));
        }
        
        return json_encode($arrayModulos);
    }

    //RETORNAR GRUPOS
    //================================================================================================

    function GetGrupos($db){
        
        $grupos = $db->fetch_all_array('select id, nome from usuarios_grupos where cliente_id = '.$_SESSION['cliente_id'].' order by nome');
        
        return json_encode($grupos);
    }
	
}


?>