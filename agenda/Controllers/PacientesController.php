<?php
/**
 * @version 1.0
 * @author Rafael Vanzo
 * @Controller
 */

//Model
require_once('Models/Pacientes.php');

//Controller Utilities (Padrão)
require_once('UtilitiesController.php');
require_once('Services/CategoriesService.php');

Class pacientesController{

    public $db;
    public $db_tabela;

function __construct($dbConnection)
{

    $this->db = DbConnection::GetDinamicConnection();  

    $this->Config = ConfigService::GetConfiguracoes();

    $this->Util = new UtilitiesController($this->db);

    $this->db_tabela = 'favorecidos';
}
    
public static function Index()
{
    
    /**Chama o Layout padrão */
    require_once 'Views/Shared/Layout.php';
    
}
 
/**
* ================================================================================= 
* ------------------------------------- LISTAR ------------------------------------  
* =================================================================================    
*/    
public function Listar()
{

    /* ===== Paramtros Localizar ServerSide*/
    $sSearch = $_GET["sSearch"];

    /* ===== CONFIGURAÇÃO CONLUNAS TABELA ===== */
    $NomeTabela = $this->db_tabela;

    //Nome da coluna que ira aparecer na janela quando clicar para excluir.
    $NomeDelete = 'nome';                 


    //Defina o NOME das COLUNAS da sua TABELA para ordenação.
    $CamposArray = array(0 => "id", 
                            1 => "nome",
                            2 => "email",
                            3 => "telefone", 
                            4 => "celular",                           
                            5 => "Opcoes"); 


    $Localizar = ' ( nome like "%'.$sSearch.'%" )';
    /* ======================================== */

    /* ===== Paramtros DataTable Ajax ServerSide */
    $sEcho = $_GET["sEcho"];

    $iDisplayStart = $_GET["iDisplayStart"];

    $iDisplayLength = $_GET["iDisplayLength"];

    $iTotalRecords = $this->db->numRows('SELECT id FROM '.$NomeTabela.' where inscricao = "cpf" ');

    $iTotalDisplayRecords = 0;


    /* ===== Ordenação Ajax */
    $sSortDir_0 = $_GET["sSortDir_0"];                                          //Tipo de ordenação crescente e decrescente.
   
    $iSortCol_0 = $_GET['iSortCol_0'];                                          //Coluna selecionada para ordernar.      

        
        /* ===== Verifica se existe alguma pesquisa no LOCALIZAR e monta a query */
        if($sSearch == ''){

           
            $query = 'SELECT * FROM '.$NomeTabela.' where inscricao = "cpf" ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength; 

            $iTotalDisplayRecords = $iTotalRecords;
       
            
        }else{

         
            $query = 'SELECT * from '.$NomeTabela.'  where inscricao = "cpf"  AND '.$Localizar.' ORDER BY '.$CamposArray[$iSortCol_0].' '.$sSortDir_0.' LIMIT '.$iDisplayStart.','.$iDisplayLength;

            $qtd = $this->db->fetch_assoc('SELECT count(id) qtd FROM '.$NomeTabela.'  where inscricao = "cpf"  AND '.$Localizar);

            $iTotalDisplayRecords = $qtd['qtd'];

       
        }

        /* ===== Monta o array da váriavel de retorno aaData e faz a pesquisa no banco de dados */
        $aaData = array();

        $Registro = $this->db->fetch_all_array($query);


        /* ===== Faz um foreach para monta o retorno dos dados para o DataTable */
        foreach($Registro as $Registro){

            
                /* ===== Opções */
                $ModaEdit = "'Edit', 'ModalCreate','".$Registro['id']."'";
                
                $ModaDelete = "'Delete', 'ModalDelete','".$Registro['id']."','".$Registro[$NomeDelete]."'";
                
                $Opcoes = '<div class="btn-group" style="width:103px;">
                                <button type="button" class="btn btn-sm btn-default" onclick="modalOpen('.$ModaEdit.')" ><i class="glyphicon glyphicon-edit"></i> Editar</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="modalOpen('.$ModaDelete.')" disabled="disabled"><i class="glyphicon glyphicon-trash"></i></button>
                            </div>';
                
                
                /****> COMENTAR SE NÃO TIVER IMAGEM (ALTERAR DE ACORDO COM O MÓDULO) ****/
                //$imagem = $this->db->fetch_assoc('SELECT arquivo FROM arquivos WHERE tabela = "'.$NomeTabela.'" AND tabela_registro_id ='.$Registro['id']);
                

                
                /****> CAMPOS DA TABELA (ALTERAR DE ACORDO COM O MÓDULO) ****/ 
                    array_push($aaData,array($CamposArray[0]=>$Registro[$CamposArray[0]],
                                            
                                            $CamposArray[1]=>$Registro[$CamposArray[1]],

                                            $CamposArray[2]=>$Registro[$CamposArray[2]], 

                                            $CamposArray[3]=>$Registro[$CamposArray[3]], 

                                            $CamposArray[4]=>$Registro[$CamposArray[4]],

                                            $CamposArray[5]=>$Opcoes));


        }


                $Retorno = array('sEcho'=>$sEcho,'iTotalRecords'=>$iTotalRecords,'iTotalDisplayRecords'=>$iTotalDisplayRecords, 'sSortDir_0'=>$sSortDir_0, 'aaData'=>$aaData);

                echo json_encode($Retorno);

}

/**
* ================================================================================= 
* ------------------------------------- CREATE ------------------------------------  
* =================================================================================  
*/    
public function Create($params)
{     
        
    try{                                                                                           
       
        $dados = new pacientes($params);  

        $this->db->query('start transaction');


                $cpf = $this->db->fetch_assoc('SELECT cpf_cnpj FROM favorecidos WHERE cpf_cnpj = "'.$params['cpf_cnpj'].'"');

                
                $dados->Fields['dtNascimento'] = $this->Util->FormatDateDB($dados->Fields['dtNascimento']);

                //Add registro no db.
                $id = $this->db->query_insert($this->db_tabela, $dados->Fields); 
                  
                echo json_encode(array('id' => $id, 'nome' => $dados->Fields['nome']));    


        $this->db->query('commit');

                                                                                               
    }catch(Exception $e){     


        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
        
        $this->db->close();
}

/**
* ================================================================================= 
* ------------------------------------- DETAILS -----------------------------------  
* =================================================================================  
*/   
public function Details($params)
{
    
    try{         

        $id = $params['Id'];
    
        $retorno = $this->db->fetch_assoc('SELECT *, DATE_FORMAT(dtNascimento, "%d/%m/%Y") as dtNascimento FROM '.$this->db_tabela.' WHERE id ='.$id);        

        echo json_encode($retorno);    

                                                            
    }catch(Exception $e){     

        
        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->db->close();
}

/**
* ================================================================================= 
* -------------------------------------- EDIT ------------------------------------- 
* ================================================================================= 
*/     
public function Edit($params)
{     
    
    try{                                                                                            
    
    $categoria = $params;
    $tag = $params;

    $dados = new pacientes($params);                                                      

    $this->db->query('start transaction');


            $dados->Fields['dtNascimento'] = $this->Util->FormatDateDB($dados->Fields['dtNascimento']);

            //Editar registros no db.
            $this->db->query_update($this->db_tabela, $dados->Fields, 'id = '.$params['Id']);            


        $this->db->query('commit'); 

                                                                                            
    }catch(Exception $e){     

        
    $this->db->query('rollback');

        $this->Util->LogErro($e->getMessage());  

    /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

}
    
    $this->db->close();
    
}

/**
* ================================================================================= 
* ------------------------------------- DELETE ------------------------------------  
* =================================================================================  
*/
 public function Delete($params)
 {

    try{                                                          
            
        $dados = array('Excluido'=> 1 , 'DtExclusao' => date('Y-m-d H:i:s'));                      
        
        $this->db->query('start transaction');

            //Marca o registro como excluído.
            $this->db->query_update($this->db_tabela, $dados, 'id = '.$params['Id']);
               

        $this->db->query('commit');

                                                            
    }catch(Exception $e){     


        $this->db->query('rollback');

            $this->Util->LogErro($e->getMessage());  
    
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */

    }
    
    $this->db->close();

}



public function VerifyCPF($params)
{
    $id = explode('/', $params['id']);

    $cpf = $this->db->fetch_assoc('SELECT id, cpf_cnpj FROM favorecidos WHERE cpf_cnpj = "'.$params['cpf_cnpj'].'"');

    if($cpf != false && $id['2'] != $cpf['id'])
    {

        $retorno = array('Situacao' => 1, 'msg' => "Paciente já cadastrado.");
    
    }else{

        $retorno = array('Situacao' => 0);
    
    }

    echo json_encode($retorno);
    
}



/**
* ================================================================================= 
* ------------------------------------- DELETE ------------------------------------  
* =================================================================================  
*/

public function AtualizarCPF($params)
{

    if(!empty($params['favorecido_cpf'])){
        $dados = array('cpf_cnpj' => $params['favorecido_cpf']);
        $this->db->query_update('favorecidos', $dados, 'id='.$params['favorecido_id']);
    }

    if(!empty($params['responsavel_cpf'])){
        $dados = array('cpf_cnpj' => $params['responsavel_cpf']);
        $this->db->query_update('favorecidos', $dados, 'id='.$params['responsavel_id']);
    }
}

} /* Fim Classe */  
?>