<?php
/**
 * @version 1.0
 * @author Rafael Vanzo
 * @Controller
 */

//Model
require_once('Models/Login.php');

//Controller Utilities (Padrão)
require_once('UtilitiesController.php');

Class LoginController{

    private $db;
    private $db_tabela;

    /**Instancia a função __construct() com os parametros do db */
    function __construct($dbConnection){

        $this->db = $dbConnection;       

        $this->Config = ConfigService::GetConfiguracoes();

        $this->db_tabela = $this->Config['Login']['db_tabela'];
    }
    
    public function Index(){
        
        /**Chama o Layout padrão */
        require_once 'Views/Shared/Layout.php';
        
    }
 

/**
* ================================================================================= 
* ---------------------------------- EFETUAR LOGIN --------------------------------  
* =================================================================================  
*/   
    public function Logar($Params, $consultaInterna = false){
        
    try{                                                                        //Inicia o processo
        
        $dados = $Params;                                                    //Pega os dados do formulário via POST. Obs.: Os nomes dos inputs tem que estar em letra minuscula.   

        if($consultaInterna == false){
            $dados['senha'] = md5($dados['senha']);                         //Criptografa a senha.
        }

        $dados = new Login($dados);
       
            $logado = $this->db->fetch_assoc('SELECT id, cliente_id, cliente_db_id, nome, email, senha, Tipo FROM '.$this->db_tabela.' WHERE email ="'.$dados->Fields['email'].'" AND senha = "'.$dados->Fields['senha'].'" AND Excluido = 0');  
            if($logado['senha'] == $dados->Fields['senha']){                

                 $_SESSION['logado'] = "e017857afedc0bc254ed4f9c88f734c2"; 
                 $_SESSION['UsuarioId'] = $logado['id'];
                 $_SESSION['Email'] = $logado['email'];
                 $_SESSION['Nome'] = $logado['nome'];
                 
                 //Especifico da aplicação
                 $_SESSION['usuarioDoutor'] = ($logado['Tipo'] == 2)? $logado['id'] : ''; 
                 $_SESSION['Tipo'] = $logado['Tipo'];


                 //Específico para integração com o WF
                 $_SESSION['cliente_id'] = $logado['cliente_id'];
                 $_SESSION['cliente_db_id'] = $logado['cliente_db_id'];
                  

                 $pag = $this->Config['Login']['paginaPrinicipal']; 
                    

                /** Login db WF cliente */
                $dbCliente = $this->db->fetch_assoc('SELECT db, db_senha FROM clientes_db WHERE cliente_id = '.$logado['cliente_id']);
               
                $_SESSION["Host"] = 'mysql.webfinancas.com';
        	    $_SESSION["dbUsuario"] = $dbCliente['db'];
                $_SESSION["dbSenha"] = $dbCliente['db'];
                $_SESSION["Db"] = $dbCliente['db'];

                               
                 $retorno = array('logado' => 1, 'url' => $pag);

            }else{ 
                $retorno = array('logado' => 0);
            }
            
            
            if($consultaInterna == false){
                echo json_encode($retorno);
            }else{
                return $retorno;
            }     
                
    }catch(Exception $e){                                                                                   //Se der alguma coisa de errado no processo do db ele será desfeito.

        $util = new UtilitiesController($this->db); 
        $util->LogErro($e->getMessage(), $dados->Fields['email']);                                       //Registra no Log
		
    }
        
		$this->db->close();
    }


/**
* ================================================================================= 
* --------------------------------- ALTERAR SENHA --------------------------------- 
* ================================================================================= 
*/     
    public function Edit($Params){     
        
try{                                                                        //Inicia o processo

        $dados = $Params;                                                //Pega os dados do formulário via POST. Obs.: Os nomes dos inputs tem que estar em letra minuscula.
              
        $dados['senha'] = md5($dados['senha']);                     //Criptografa a senha.      

        $dados = new Login($dados);                                 //Instacia o Model.
        
			$this->db->query('start transaction');
            $this->db->query_update($this->db_tabela, $dados->Fields, 'id = '.$Params['Id']);
            $this->db->query('commit'); 
                                                                            //Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message");
}catch(Exception $e){                                                       //Se der alguma coisa de errado no processo do db ele será desfeito.
            
    $this->db->query('rollback');
    $util = new UtilitiesController($this->db); 
    $util->LogErro($e->getMessage());                                       //Registra no Log
		
}
        
		$this->db->close();
    }

/**
* ================================================================================= 
* -------------------------------- RECUPERAR SENHA -------------------------------- 
* ================================================================================= 
*/     
   public function RecuperarSenha($Params){     
        
try{                                                                        //Inicia o processo

        $dados = $Params;                                                //Pega os dados do formulário via POST. Obs.: Os nomes dos inputs tem que estar em letra minuscula.       
        
        $novaSenha = UtilitiesController::GeradorSenha();
        
        $dados['senha'] = md5($novaSenha);                              //Criptografa a senha.      
        
        $dados = new Login($dados);                                 //Instacia o Model.

        $email = $dados->Fields['email'];

        
            $this->db->query('start transaction');   
            
            $verificarEmail = $this->db->fetch_assoc('SELECT email FROM '.$this->db_tabela.' WHERE email = "'.$email.'"');
            
            if($verificarEmail['email'] == $email){

     
                $this->db->query_update($this->db_tabela, array('senha' => $dados->Fields['senha']), 'email = "'.$email.'"');
            
                $conteudoEmail = 'Login: <b>'.$email.'</b> <br> Senha: <b>'.$novaSenha.'</b><br>';

                UtilitiesController::emailEnviar($email, 'Recuperação de senha', $conteudoEmail);

                                
                        $retorno = array('situacao' => 1);
                        
                    }else{ 

                        $retorno = array('situacao' => 0);
                }

    
                echo json_encode($retorno);

            $this->db->query('commit');
                                                                            //Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message");
}catch(Exception $e){                                                       //Se der alguma coisa de errado no processo do db ele será desfeito.
            
    $this->db->query('rollback');
    $util = new UtilitiesController($this->db); 
    $util->LogErro($e->getMessage(), $dados->Fields['email']);          //Registra no Log, somente no LoginController ele registra o email porque não esta logado.
		
}
        
		$this->db->close();
    }




/**
* ================================================================================= 
* ------------------------------------- LOGOUT ------------------------------------ 
* ================================================================================= 
*/     
    public function Logout(){     
        
try{                                                                        //Inicia o processo
     
       session_destroy();

        $retorno = array('url' => $this->Config['Layout']['Base']['urlBase'].'Login');

       echo json_encode($retorno);
                                                                            
}catch(Exception $e){                                                       //Se der alguma coisa de errado no processo do db ele será desfeito.
            
    $this->db->query('rollback');
    $util = new UtilitiesController($this->db); 
    $util->LogErro($e->getMessage());                                       //Registra no Log
		
}
        
		$this->db->close();
    }


/**
* ================================================================================= 
* ----------------------------------- LOGAR DO WF --------------------------------- 
* ================================================================================= 
*/     

public function LoginExterno($params)
{

    $dados = explode('_', $params['Id']);

    $login = $this->db->fetch_assoc('SELECT email, senha FROM usuarios WHERE cliente_id ='.$dados['0'].' AND email = "'.$dados['1'].'"');


    if($login == false){
        
        echo "<script>alert('Não foi possível conectar. Por favor acesse a agenda através do Login e Senha.); javascript:window.close();</script>";
    break;
    }
    
        $retorno = self::Logar($login, true);      


    if($retorno['logado'] == 1){
 
         header('Location: /'.$retorno["url"]);

    }else{

        echo "<script>alert('Não foi possível conectar. Por favor acesse a agenda através do Login e Senha.); javascript:window.close();</script>";     
    }

       
}




}

?>