<?php
/**
 * @version 1.1
 * @author Rafael Vanzo e Fabio Moreto
 * @RouterConfig
 */    


Class Router
{          

    /* Atributos MVC - Controller, Action e Param */
    private $Config;
    private $db;
    protected $Controller;
    protected $Action = "Index";
    protected $Param = array();    

    /* Cria as rotas */
    public function __construct()
    {       

        /* inicio: Instancia a classe de configurações em jSon e atribui as configurações. */
        $ConfigService = new ConfigService;
        $this->Config = $ConfigService->GetConfiguracoes();
        /* fim: Instancia a classe de configurações em jSon e atribui as configurações. */


        //inicio: Pega parâmetros recebidos na requisição

        $strParams = file_get_contents('php://input');
        $arrayParams = json_decode($strParams, true);
        //fim: Pega parâmetros recebidos na requisição          

        /************************************/

        //inicio: Verifica se o arquivo Controller existe
        if(!empty($_GET["Controller"]))
        {
            if(!file_exists("Controllers/" . $_GET["Controller"] . "Controller.php"))
            {
                echo json_encode(array("status"=>false,"Mensagem"=>"Controller '$_GET[Controller]' inexistente."));
                return false;
            }
            
            //Chama o Controller
            $this->Controller = $_GET["Controller"];
        }else{

            /* inicio: Atribui o controller que deve ser exibido quando não existe nenhum declarado no link. */        
            $this->Controller = $this->Config['Layout']['Base']['controller'];        
            /* fim: Atribui o controller que deve ser exibido quando não existe nenhum declarado no link. */  
        }
        //fim: Verifica se o arquivo Controller existe


        /*inicio: Atribui a conexão */
        $Db = new DbConnection();
        $this->db = $Db->GetConnection();
        /*fim: Atribui a conexão */

        //inicio: Instancia a classe controller
        require_once "Controllers/" . $this->Controller . "Controller.php";
        $Controller = $this->Controller.'Controller';
        $this->Controller = new $Controller($this->db);
        //fim: Instancia a classe controller
        
        //inicio: Verifica se a action existe
        if(!empty($_GET["Action"]))
        { 
            if(!method_exists($this->Controller, $_GET["Action"]))
            {
                echo json_encode(array("status"=>false,"Mensagem"=>"Action '$_GET[Action]' inexistente."));
                return false;
            }

            $this->Action = $_GET["Action"];
        
        }else{ 
            
            //inicio: Atribui o Action que deve ser exibido quando não existe nenhum declarado no link.        
            $this->Action = $this->Config['Layout']['Base']['action'];        
            //fim: Atribui o Action que deve ser exibido quando não existe nenhum declarado no link.  
        
        }        
        //fim: Verifica se a Action existe
        

        //Pega todos os Parametros via GET e insere em um array único
        $arrayRequest = $_REQUEST ? $_REQUEST : array();
        $arrayRequest = self::removeParams($arrayRequest);

        // Verifica se os parammetros existem via POST e PUT, se existir cria um array com eles
        $arrayParams = $arrayParams ? $arrayParams : array();
        
        //Atribui a váriavel global $this->Param
        
        //Atribui os parametros na variavel global $this->Param e junta os arrays com todas os parametros GET, POST e PUT.
        $this->Param = array_merge($arrayRequest, $arrayParams);
        //print_r($this->Param);
        
        //Chama o Controller, a Action e os Parametros
        call_user_func(array($this->Controller, $this->Action), $this->Param);

    }


    private function removeParams($Params)
    {

        unset($Params['Controller']);
        unset($Params['Action']);
        unset($Params['Submit']);
        
        unset($Params['_ga']);
        unset($Params['PHPSESSID']);
        unset($Params['_gid']);
        unset($Params['_gat_gtag_UA_125586050_1']);

        return $Params; 
    }

}
?>