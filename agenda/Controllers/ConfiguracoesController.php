<?php
/**
 * @version 1.0
 * @author Rafael Vanzo
 * @Controller
 */

//Model
require_once('Models/Configuracoes.php');

//Controller Utilities (Padrão)
require_once('UtilitiesController.php');
require_once('Services/CategoriesService.php');

Class ConfiguracoesController{

    public $db;
    public $dbUsuario;
    public $db_tabela;

function __construct($dbConnection)
{

    $this->db = DbConnection::GetDinamicConnection(); 

    $this->dbUsuario = DbConnection::GetConnection();  

    $this->Config = ConfigService::GetConfiguracoes();

    $this->Util = new UtilitiesController($this->db);

    $this->db_tabela = 'consultas';
}
    
public function Index()
{

    /**Chama o Layout padrão */
    require_once 'Views/Shared/Layout.php';
    
}
 

/**
* ========================================================================================================== 
* ------------------------------------- LISTAR CONSULTAS / PROCEDIMENTOS------------------------------------  
* ==========================================================================================================    
*/    
public function ListarConsultas()
{

    /* ===== Paramtros Localizar ServerSide*/
    $sSearch = $_GET["sSearch"];

    /* ===== CONFIGURAÇÃO CONLUNAS TABELA ===== */
    $NomeTabela = 'configConsultaProc';

    //Nome da coluna que ira aparecer na janela quando clicar para excluir.
    $NomeDelete = 'Descricao';                 


    //Defina o NOME das COLUNAS da sua TABELA para ordenação.
    $CamposArray = array(0 => "Id", 
                            1 => "Tipo",
                            2 => "Descricao",                           
                            3 => "Opcoes"); 


    $Localizar = '( Descricao like "%'.$sSearch.'%")';
    /* ======================================== */

    /* ===== Paramtros DataTable Ajax ServerSide */
    $sEcho = $_GET["sEcho"];

    $iDisplayStart = $_GET["iDisplayStart"];

    $iDisplayLength = $_GET["iDisplayLength"];

    //$iTotalRecords = $this->db->numRows('SELECT Id FROM '.$NomeTabela.' WHERE Excluido = 0', $this->db);

    $qtd_registros = $this->db->fetch_assoc('SELECT count(Id) qtd FROM '.$NomeTabela.' WHERE Excluido = 0');

    $iTotalRecords = $qtd_registros['qtd'];

    $iTotalDisplayRecords = 0;


    /* ===== Ordenação Ajax */
    $sSortDir_0 = $_GET["sSortDir_0"];                                          //Tipo de ordenação crescente e decrescente.
   
    $iSortCol_0 = $_GET['iSortCol_0'];                                          //Coluna selecionada para ordernar.      

        
        /* ===== Verifica se existe alguma pesquisa no LOCALIZAR e monta a query */
        if($sSearch == ''){

           
            $query = 'SELECT * FROM '.$NomeTabela.' WHERE Excluido = 0 ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength; 

            $iTotalDisplayRecords = $iTotalRecords;
       
            
        }else{

         
            $query = 'SELECT * from '.$NomeTabela.' WHERE Excluido = 0 AND '.$Localizar.' ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength;

            $qtd = $this->db->fetch_assoc('SELECT count(Id) qtd FROM '.$NomeTabela.' WHERE Excluido = 0 AND '.$Localizar);

            $iTotalDisplayRecords = $qtd['qtd'];

       
        }

        /* ===== Monta o array da váriavel de retorno aaData e faz a pesquisa no banco de dados */
        $aaData = array();

        $Registro = $this->db->fetch_all_array($query);


        /* ===== Faz um foreach para monta o retorno dos dados para o DataTable */
        foreach($Registro as $Registro){

            
                /* ===== Opções */
                $ModaEdit = "'Edit', 'ModalConsultas','".$Registro['Id']."'";
                
                $ModaDelete = "'Delete', 'ModalDeleteConsultas','".$Registro['Id']."','".$Registro[$NomeDelete]."'";
                
                $Opcoes = '<div class="btn-group" style="width:103px;">
                                <button type="button" class="btn btn-sm btn-default" onclick="modalOpen('.$ModaEdit.')" ><i class="glyphicon glyphicon-edit"></i> Editar</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="modalOpen('.$ModaDelete.')"><i class="glyphicon glyphicon-trash"></i></button>
                            </div>';
                
                
                /****> COMENTAR SE NÃO TIVER IMAGEM (ALTERAR DE ACORDO COM O MÓDULO) ****/
                //$imagem = $this->db->fetch_assoc('SELECT arquivo FROM arquivos WHERE tabela = "'.$NomeTabela.'" AND tabela_registro_id ='.$Registro['Id']);
                

                 /* Posição 
                $TotalRegistro = $this->db->numRows('SELECT Posicao FROM banner WHERE Excluido = 0');

                $Posicao = "'".$Registro[$CamposArray[4]]."', '".$TotalRegistro."'";                 
                

                $Subir = '<button type="button" class="btn btn-default spinner-up" onClick="Subir('.$Posicao.')">
                            <i class="fa fa-angle-up"></i>
                            </button> &nbsp;&nbsp;&nbsp; <b style="font-size:14px;"> ';
                    
                $Descer = ' </b> &nbsp;&nbsp;&nbsp; <button type="button" class="btn btn-default spinner-down" onClick="Descer('.$Posicao.')">
                            <i class="fa fa-angle-down"></i>
                            </button>';
                */


                $Tipo = ($Registro[$CamposArray[1]] == 1)? 'Consulta' : 'Procedimento';  
                
                /****> CAMPOS DA TABELA (ALTERAR DE ACORDO COM O MÓDULO) ****/ 
                    array_push($aaData,array($CamposArray[0]=>$Registro[$CamposArray[0]],
                                           
                                            $CamposArray[1]=>$Tipo, 

                                            $CamposArray[2]=>$Registro[$CamposArray[2]], 
                                            
                                            $CamposArray[3]=>$Opcoes));



        }


                $Retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords, 'sSortDir_0'=>$sSortDir_0, 'aaData'=>$aaData);

                echo json_encode($Retorno);

}



/**
* ================================================================================= 
* ------------------------------------- LISTAR ------------------------------------  
* =================================================================================    
*/    
public function ListarUsuarios()
{

    /* ===== Paramtros Localizar ServerSide*/
    $sSearch = $_GET["sSearch"];

    /* ===== CONFIGURAÇÃO CONLUNAS TABELA ===== */
    $NomeTabela = 'usuarios';

    //Nome da coluna que ira aparecer na janela quando clicar para excluir.
    $NomeDelete = 'Nome';                 


    //Defina o NOME das COLUNAS da sua TABELA para ordenação.
    $CamposArray = array(0 => "Id", 
                            1 => "Nome",
                            2 => "Email",                          
                            3 => "Opcoes"); 


    $Localizar = '( Nome like "%'.$sSearch.'%" or
                    Email like "%'.$sSearch.'%")';
    /* ======================================== */

    /* ===== Paramtros DataTable Ajax ServerSide */
    $sEcho = $_GET["sEcho"];

    $iDisplayStart = $_GET["iDisplayStart"];

    $iDisplayLength = $_GET["iDisplayLength"];

    $iTotalRecords = $this->dbUsuario->numRows('SELECT Id FROM '.$NomeTabela.' WHERE Tipo = 1 AND Excluido = 0');

    $iTotalDisplayRecords = 0;


    /* ===== Ordenação Ajax */
    $sSortDir_0 = $_GET["sSortDir_0"];                                          //Tipo de ordenação crescente e decrescente.
   
    $iSortCol_0 = $_GET['iSortCol_0'];                                          //Coluna selecionada para ordernar.      

        
        /* ===== Verifica se existe alguma pesquisa no LOCALIZAR e monta a query */
        if($sSearch == ''){

           
            $query = 'SELECT * FROM '.$NomeTabela.' WHERE Tipo = 1 AND Excluido = 0 ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength; 

            $iTotalDisplayRecords = $iTotalRecords;
       
            
        }else{

         
            $query = 'SELECT * from '.$NomeTabela.' where Tipo = 1 AND Excluido = 0 AND '.$Localizar.' ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength;

            $qtd = $this->dbUsuario->fetch_assoc('SELECT count(Id) qtd FROM '.$NomeTabela.' WHERE Tipo = 1 AND Excluido = 0 AND '.$Localizar);

            $iTotalDisplayRecords = $qtd['qtd'];

       
        }

        /* ===== Monta o array da váriavel de retorno aaData e faz a pesquisa no banco de dados */
        $aaData = array();

        $Registro = $this->dbUsuario->fetch_all_array($query);


        /* ===== Faz um foreach para monta o retorno dos dados para o DataTable */
        foreach($Registro as $Registro){

            
                /* ===== Opções */
                $ModaEdit = "'Edit', 'ModalUsuarios','".$Registro['Id']."'";
                
                $ModaDelete = "'Delete', 'ModalDeleteUsuarios','".$Registro['Id']."','".$Registro[$NomeDelete]."'";
                
                $Opcoes = '<div class="btn-group" style="width:103px;">
                                <button type="button" class="btn btn-sm btn-default" onclick="modalOpen('.$ModaEdit.')" ><i class="glyphicon glyphicon-edit"></i> Editar</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="modalOpen('.$ModaDelete.')"><i class="glyphicon glyphicon-trash"></i></button>
                            </div>';
                
                
                /****> COMENTAR SE NÃO TIVER IMAGEM (ALTERAR DE ACORDO COM O MÓDULO) ****/
               // $imagem = $this->db->fetch_assoc('SELECT arquivo FROM arquivos WHERE tabela = "'.$NomeTabela.'" AND tabela_registro_id ='.$Registro['Id']);
                

                /* Posição 
                $TotalRegistro = $this->db->numRows('SELECT Posicao FROM banner WHERE Excluido = 0');

                $Posicao = "'".$Registro[$CamposArray[4]]."', '".$TotalRegistro."'";                 
                

                $Subir = '<button type="button" class="btn btn-default spinner-up" onClick="Subir('.$Posicao.')">
                            <i class="fa fa-angle-up"></i>
                            </button> &nbsp;&nbsp;&nbsp; <b style="font-size:14px;"> ';
                    
                $Descer = ' </b> &nbsp;&nbsp;&nbsp; <button type="button" class="btn btn-default spinner-down" onClick="Descer('.$Posicao.')">
                            <i class="fa fa-angle-down"></i>
                            </button>';
                */
                
                /****> CAMPOS DA TABELA (ALTERAR DE ACORDO COM O MÓDULO) ****/ 
                    array_push($aaData,array($CamposArray[0]=>$Registro[$CamposArray[0]],
                                           
                                            $CamposArray[1]=>$Registro[$CamposArray[1]], 

                                            $CamposArray[2]=>$Registro[$CamposArray[2]], 
                                            
                                            $CamposArray[3]=>$Opcoes));


        }


                $Retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords, 'sSortDir_0'=>$sSortDir_0, 'aaData'=>$aaData);

                echo json_encode($Retorno);

}



/**
* ======================================================================================== 
* ------------------------------------- LISTAR DOUTOR ------------------------------------  
* ========================================================================================    
*/    
public function ListarDoutor()
{

    /* ===== Paramtros Localizar ServerSide*/
    $sSearch = $_GET["sSearch"];

    /* ===== CONFIGURAÇÃO CONLUNAS TABELA ===== */
    $NomeTabela = 'usuarios';

    //Nome da coluna que ira aparecer na janela quando clicar para excluir.
    $NomeDelete = 'nome';                 


    //Defina o NOME das COLUNAS da sua TABELA para ordenação.
    $CamposArray = array(0 => "id", 
                        1 => "nome",
                        2 => "email",                           
                        3 => "Opcoes"); 


    $Localizar = '( email like "%'.$sSearch.'%" OR
                    nome like "%'.$sSearch.'%")';
    /* ======================================== */

    /* ===== Paramtros DataTable Ajax ServerSide */
    $sEcho = $_GET["sEcho"];

    $iDisplayStart = $_GET["iDisplayStart"];

    $iDisplayLength = $_GET["iDisplayLength"];

    $iTotalRecords = $this->dbUsuario->numRows('SELECT id FROM '.$NomeTabela.' WHERE cliente_id = '.$_SESSION['cliente_id'].' AND grupo_id = 78  AND Excluido = 0');

    $iTotalDisplayRecords = 0;


    /* ===== Ordenação Ajax */
    $sSortDir_0 = $_GET["sSortDir_0"];                                          //Tipo de ordenação crescente e decrescente.
   
    $iSortCol_0 = $_GET['iSortCol_0'];                                          //Coluna selecionada para ordernar.      

        
        /* ===== Verifica se existe alguma pesquisa no LOCALIZAR e monta a query */
        if($sSearch == ''){

           
            $query = 'SELECT * FROM '.$NomeTabela.' WHERE  cliente_id = '.$_SESSION['cliente_id'].' AND grupo_id = 78  AND Excluido = 0 ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength; 

            $iTotalDisplayRecords = $iTotalRecords;
       
            
        }else{

         
            $query = 'SELECT * from '.$NomeTabela.' where  cliente_id = '.$_SESSION['cliente_id'].' AND grupo_id = 78 AND Excluido = 0 AND '.$Localizar.' ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength;

            $qtd = $this->dbUsuario->fetch_assoc('SELECT count(id) qtd FROM '.$NomeTabela.' WHERE cliente_id = '.$_SESSION['cliente_id'].' AND grupo_id = 78  AND Excluido = 0 AND '.$Localizar);

            $iTotalDisplayRecords = $qtd['qtd'];

       
        }

        /* ===== Monta o array da váriavel de retorno aaData e faz a pesquisa no banco de dados */
        $aaData = array();

        $Registro = $this->dbUsuario->fetch_all_array($query);


        /* ===== Faz um foreach para monta o retorno dos dados para o DataTable */
        foreach($Registro as $Registro){

            
                /* ===== Opções */
                $ModaEdit = "'Edit', 'ModalDoutor','".$Registro['id']."'";
                
                $ModaDelete = "'Delete', 'ModalDeleteUsuarios','".$Registro['id']."','".$Registro[$NomeDelete]."'";
                
                $Opcoes = '<div class="btn-group" style="width:103px;">
                                <button type="button" class="btn btn-sm btn-default" onclick="modalOpen('.$ModaEdit.')" ><i class="glyphicon glyphicon-edit"></i> Editar</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="modalOpen('.$ModaDelete.')"><i class="glyphicon glyphicon-trash"></i></button>
                            </div>';
                
                
                /****> COMENTAR SE NÃO TIVER IMAGEM (ALTERAR DE ACORDO COM O MÓDULO) ****/
                //$imagem = $this->db->fetch_assoc('SELECT arquivo FROM arquivos WHERE tabela = "'.$NomeTabela.'" AND tabela_registro_id ='.$Registro['Id']);
                

                 /* Posição 
                $TotalRegistro = $this->db->numRows('SELECT Posicao FROM banner WHERE Excluido = 0');

                $Posicao = "'".$Registro[$CamposArray[4]]."', '".$TotalRegistro."'";                 
                

                $Subir = '<button type="button" class="btn btn-default spinner-up" onClick="Subir('.$Posicao.')">
                            <i class="fa fa-angle-up"></i>
                            </button> &nbsp;&nbsp;&nbsp; <b style="font-size:14px;"> ';
                    
                $Descer = ' </b> &nbsp;&nbsp;&nbsp; <button type="button" class="btn btn-default spinner-down" onClick="Descer('.$Posicao.')">
                            <i class="fa fa-angle-down"></i>
                            </button>';
                */
                
                /****> CAMPOS DA TABELA (ALTERAR DE ACORDO COM O MÓDULO) ****/ 
                array_push($aaData,array($CamposArray[0]=>$Registro[$CamposArray[0]],
                                           
                                        $CamposArray[1]=>$Registro[$CamposArray[1]], 

                                        $CamposArray[2]=>$Registro[$CamposArray[2]], 
                                        
                                        $CamposArray[3]=>$Opcoes));



        }


                $Retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords, 'sSortDir_0'=>$sSortDir_0, 'aaData'=>$aaData);

                echo json_encode($Retorno);

}




/**
* ========================================================================================== 
* ------------------------------------- CREATE USUARIOS ------------------------------------  
* ==========================================================================================  
*/    
public function CreateUsuarios($params)
{     
        
    try{                                                                                           
       
        $dados = new Usuarios($params);  

        unset($dados->Fields['Id']);

        $this->dbUsuario->query('start transaction');

                $dados->Fields['senha'] = md5($dados->Fields['SenhaShow']);

                //Add campos para o usuário Doutor na Agenda dentro do db do WF
                $dados->Fields['grupo_id'] = 78;
                $dados->Fields['cliente_id'] = $_SESSION['cliente_id'];
                $dados->Fields['situacao'] = 1;
                $dados->Fields['Tipo'] = 2;


                $email = $this->dbUsuario->fetch_assoc('SELECT email FROM usuarios WHERE email = "'.$params['email'].'"');

                if($email){

                    echo json_encode(array('situacao' => 0, 'Msg' => 'Não é possível cadastrar o e-mail, por favor digite um outro e-mail.'));

                }else{

                    //Add registro no db.
                    $id = $this->dbUsuario->query_insert('usuarios', $dados->Fields); 

                }


            

        $this->dbUsuario->query('commit');

                                                                                               
    }catch(Exception $e){     


        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
        
        $this->db->close();
}

/**
* ========================================================================================== 
* ------------------------------------- DETAILS USUARIOS -----------------------------------  
* ==========================================================================================  
*/   
public function DetailsUsuarios($params)
{
    
    try{         

        $id = $params['Id'];
    
        $retorno = $this->dbUsuario->fetch_assoc('SELECT * FROM usuarios WHERE id ='.$id);        

        echo json_encode($retorno);    

                                                            
    }catch(Exception $e){     

        
        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->dbUsuario->close();
}

/**
* ========================================================================================== 
* -------------------------------------- EDIT USUARIOS ------------------------------------- 
* ========================================================================================== 
*/     
public function EditUsuarios($params)
{     
    
    try{                                                                                            
    
    $categoria = $params;
    $tag = $params;

    $dados = new Usuarios($params);                                                      

    $this->dbUsuario->query('start transaction');

            $dados->Fields['Senha'] = md5($dados->Fields['SenhaShow']);


            $emailAntigo = $this->dbUsuario->fetch_assoc('SELECT email FROM usuarios WHERE id = '.$params['Id']);


            if($emailAntigo['email'] != $params['email']){

                
                $email = $this->dbUsuario->fetch_assoc('SELECT email FROM usuarios WHERE email = "'.$params['email'].'"');


                if($email){

                    echo json_encode(array('situacao' => 0, 'Msg' => 'Não é possível cadastrar o e-mail, por favor digite um outro e-mail.'));

                }else{

                    //Editar registros no db.
                    $this->dbUsuario->query_update('usuarios', $dados->Fields, 'id = '.$params['Id']);            

                }

            }else{

                    //Editar registros no db.
                    $this->dbUsuario->query_update('usuarios', $dados->Fields, 'id = '.$params['Id']);     

            }

        $this->dbUsuario->query('commit'); 

                                                                                            
    }catch(Exception $e){     

        
    $this->dbUsuario->query('rollback');

        $this->Util->LogErro($e->getMessage());  

    /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

}
    
    $this->dbUsuario->close();
    
}

/**
* ================================================================================= 
* ------------------------------------- DELETE ------------------------------------  
* =================================================================================  
*/
 public function DeleteUsuarios($params)
 {

    try{                                                          
            
        $dados = array('Excluido'=> 1 , 'DtExclusao' => date('Y-m-d H:i:s'));                      
        
        $this->dbUsuario->query('start transaction');

            //Marca o registro como excluído.
            $this->dbUsuario->query_update('usuarios', $dados, 'id = '.$params['Id']);

            //Excluir posição.
            $this->Util->PositionDelete($params['Id']);
                

        $this->dbUsuario->query('commit');

                                                            
    }catch(Exception $e){     


        $this->dbUsuario->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->dbUsuario->close();

}




/**
* ============================================================================================
* ------------------------------------- CREATE CONSULTA --------------------------------------  
* ============================================================================================
*/    
public function CreateConsultas($params)
{     
        
    try{      
        
        $params['Valor'] = str_replace(",",".", $params['Valor']);
       
        $dados = new ConsultasProcedimentos($params);  

        $this->db->query('start transaction');

        

                //Add registro no db.
                $id = $this->db->query_insert('configConsultaProc', $dados->Fields);             

        $this->db->query('commit');

                                                                                               
    }catch(Exception $e){     


        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
        
        $this->db->close();
}

/**
* ============================================================================================
* ------------------------------------- CREATE CONSULTA --------------------------------------  
* ============================================================================================
*/  
public function DetailsConsultas($params)
{
    
    try{         
        
        $id = $params['Id'];
    
        $retorno = $this->db->fetch_assoc('SELECT * FROM configConsultaProc WHERE id ='.$id);        

        $retorno['Valor'] = str_replace(".",",", $retorno['Valor']);

        echo json_encode($retorno);    

                                                            
    }catch(Exception $e){     

        
        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->db->close();
}

/**
* ============================================================================================
* ------------------------------------- CREATE CONSULTA --------------------------------------  
* ============================================================================================
*/     
public function EditConsultas($params)
{     
    
    try{                  

        $params['Valor'] = str_replace(",",".", $params['Valor']);
        
    $dados = new ConsultasProcedimentos($params);                                                      

    $this->db->query('start transaction');


            //Editar registros no db.
            $this->db->query_update('configConsultaProc', $dados->Fields, 'id = '.$params['Id']);            

        $this->db->query('commit'); 

                                                                                            
    }catch(Exception $e){     

        
    $this->db->query('rollback');

        $this->Util->LogErro($e->getMessage());  

    /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

}
    
    $this->db->close();
    
}

/**
* ============================================================================================
* ------------------------------------- CREATE CONSULTA --------------------------------------  
* ============================================================================================
*/  
 public function DeleteConsultas($params)
 {

    try{                                                          
            
        $dados = array('Excluido'=> 1 , 'DtExclusao' => date('Y-m-d H:i:s'));                      
        
        $this->db->query('start transaction');

            //Marca o registro como excluído.
            $this->db->query_update('configConsultaProc', $dados, 'id = '.$params['Id']);

            //Excluir posição.
            $this->Util->PositionDelete($params['Id']);
                

        $this->db->query('commit');

                                                            
    }catch(Exception $e){     


        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->db->close();

}

/**
* ============================================================================================
* ------------------------------------- CREATE CONSULTA --------------------------------------  
* ============================================================================================
*/ 

    public function ProcedimentoValor($params)
    {
    
        try{                                                                          
            
            $this->db->query('start transaction');
    
            if($params['id'] !== ""){

                //Marca o registro como excluído.
                $retorno = $this->db->fetch_assoc("SELECT Valor FROM configConsultaProc WHERE id = ".$params['id']);

                $retorno['Valor'] = $this->Util->FormatNumber($retorno['Valor']);

            }else{    

                $retorno['Valor'] = '0,00';
            }
            
            echo json_encode($retorno);
            

            $this->db->query('commit');
    
                                                                
        }catch(Exception $e){     
    
    
            $this->db->query('rollback');
    
                $this->Util->LogErro($e->getMessage());  
        
            /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */
    
        }
        
        $this->db->close();

    }

} /* Fim Classe */  
?>