<?php
/**
 * Class Utilities para chamar funções comuns em todo o sistema.
 * 
 * @version 2.0
 * @author Rafael Vanzo
 * @Controller
 */

require_once("Assets/swiftMailer/lib/swift_required.php");
require_once("Services/ServiceClassLoader.php");

Class UtilitiesController
{

        private $db;

        private $Config;

        private $CaminhoUpload;       


    function __construct($db){

        $this->db = $db;

        $this->Config = ConfigService::GetConfiguracoes();

        $this->CaminhoUpload = $this->Config['Utilities']['caminhoUploads'];

    }
    

    /** 
    * ================================================================================= 
    * ---------------------------------- DB ARQUIVOS ---------------------------------- 
    * ================================================================================= 
    */ 

    /* ARQUIVOS CREATE */
    public function Arquivo_db_create($pasta, $name, $tabela, $registroId, $Principal)
    {

        $FilesService = new FilesService($this->db);

        return $FilesService->Create($pasta, $name, $tabela, $registroId, $Principal);

    }


    /* ARQUIVOS EDIT */
    public function Arquivo_db_editar($pasta, $name, $tabela, $registroId, $Principal = false)
    {

        $FilesService = new FilesService($this->db);

        return $FilesService->Edit($pasta, $name, $tabela, $registroId, $Principal = false);

    }


    /* ARQUIVOS DELETE */
    public function ArquivoDelete($Caminho, $Documento)
    {

        $FilesService = new FilesService($this->db);

        return $FilesService->Delete($Caminho, $Documento);

    }


    /* UPLOAD DROPZONE */
    public function Uploads($Params)
    {

        $FilesService = new FilesService($this->db);

        echo $FilesService->Uploads($Params['pasta'], $Params['larguraThumb']);

    }


    public function UploadsCreate($dzNome, $Pasta, $id, $tabela)
    {

        $FilesService = new FilesService($this->db);

        $FilesService->UploadsCreate($dzNome, $Pasta, $id, $tabela);
        
    }

    public function UploadsEdit($dzNome, $Pasta, $id, $tabela)
    {

        $FilesService = new FilesService($this->db);

        $FilesService->UploadsEdit($dzNome, $Pasta, $id, $tabela);
        
    }

    /** 
    * ================================================================================= 
    * ------------------------------------- IMAGENS ----------------------------------- 
    * ================================================================================= 
    */

    /* SUMMER NOTE IMG */
    public function SummerNoteImg()
    {

        $FilesService = new FilesService($this->db);

        echo $FilesService->SummerNoteImg('/../Uploads/Editor');

    }


    /* SLIMUPLOAD */
    public function SlimUpload($Params, $RegistrId, $Tp, $larguraThumb = false)
    {

        $FilesService = new FilesService($this->db);

        $return =  $FilesService->SlimUpload($Params, $RegistrId, $Tp, $larguraThumb = false);

        return $return;

    }


    /** 
    * ================================================================================= 
    * ----------------------------------- CATEGORIAS ---------------------------------- 
    * ================================================================================= 
    */

    public function CreateCategorias($params)
    {

        $categorias = new CategoriesService($this->db);

        $return = $categorias->Create($params);

        echo $return;

    }


    public function EditCategorias($params)
    {

        $categorias = new CategoriesService($this->db);

        $return = $categorias->Edit($params);

        echo $return;

    }


    public function DeleteCategorias($params)
    {

        $categorias = new CategoriesService($this->db);

        $return = $categorias->Delete($params);

        echo $return;

    }


    public function ListarCategorias($params)
    {

        $categorias = new CategoriesService($this->db);

        $return = $categorias->Listar($params);

        echo $return;

    }


    public function ListarCategoriaPai($params)
    {

        $categorias = new CategoriesService($this->db);

        $return = $categorias->ListarPai($params);

        echo $return;

    }


    public function ListarForm($params)
    {

        $categorias = new CategoriesService($this->db);

        $return = $categorias->ListarForm($params);

        echo $return;

    }

    /* *
     * Criação de relacionamento da tabela com a categoria especifica.     * 
     * */
    public function CreateCatRelation($tabela, $params, $id)
    {

        $categorias = new CategoriesService($this->db);

        $categorias->CreateCatRelation($tabela, $params, $id);

    }

    public function EditCatRelation($tabela, $params, $id)
    {

        $categorias = new CategoriesService($this->db);

        $categorias->EditCatRelation($tabela, $params, $id);

    }

    public function DetailsCatRelation($tabela, $id)
    {

        $categorias = new CategoriesService($this->db);

        $return = $categorias->DetailsCatRelation($tabela, $id);

        return $return;

    }

    /** 
    * ================================================================================= 
    * -------------------------------------- TAGS ------------------------------------- 
    * ================================================================================= 
    */

    public function CreateTags($params)
    {

        $tags = new TagsService($this->db);

        $return = $tags->Create($params);

        echo $return;

    }


    public function EditTags($params)
    {

        $tags = new TagsService($this->db);

        $return = $tags->Edit($params);

        echo $return;

    }


    public function DeleteTags($params)
    {

        $tags = new TagsService($this->db);

        $return = $tags->Delete($params);

        echo $return;

    }


    public function ListarTags($params)
    {

        $tags = new TagsService($this->db);

        $return = $tags->Listar($params);

        echo $return;

    }


    public function ListarFormTags($params)
    {

        $tags = new TagsService($this->db);

        $return = $tags->ListarForm($params);

        echo $return;

    }

    /* *
     * Criação de relacionamento da tabela com a tags especifica.     * 
     * */
    public function CreateTagRelation($tabela, $params, $id)
    {

        $tags = new TagsService($this->db);

        $tags->CreateTagRelation($tabela, $params, $id);

    }

    public function EditTagRelation($tabela, $params, $id)
    {

        $tags = new TagsService($this->db);

        $tags->EditTagRelation($tabela, $params, $id);

    }

    public function DetailsTagRelation($tabela, $id)
    {

        $tags = new TagsService($this->db);

        $return = $tags->DetailsTagRelation($tabela, $id);

        return $return;

    }


    /** 
    * ================================================================================= 
    * --------------------- ORDENAÇÃO DA POSIÇÃO (POSITION SERVICE) ------------------- 
    * ================================================================================= 
    */

    public function PositionCreate()
    {

        $position = new PositionService($this->db);

        $return = $position->Create();

        return $return;

    }

    public function PositionChange($params)
    {

        $position = new PositionService($this->db);

        $return = $position->Change($params);

    }

    public function PositionDelete($params)
    {

        $position = new PositionService($this->db);

        $return = $position->Delete($params);

    }


    /** 
    * ================================================================================= 
    * ---------------- ENVIO DE ARQUIVO PARA OUTRO SERVIDOR C/ CURL ------------------- 
    * ================================================================================= 
    */

    public function SendFilesCurl($url, $tmpFile, $type, $name, $CaminhoPasta)
    {

        $return = CURLService::SendFiles($url, $tmpFile, $type, $name, $CaminhoPasta);

        return $return;

    }



    /** 
    * ================================================================================= 
    * ------------------ FUNÇÃO PARA PEGAR INTERVALO DE UMA STRING -------------------- 
    * ================================================================================= 
    * Exemplo:
    * GetBetween("a","c","abc");
    **/

    function GetBetween($var1="",$var2="",$pool)
    {
        $temp1 = strpos($pool,$var1)+strlen($var1);

        $result = substr($pool,$temp1,strlen($pool));

        $dd=strpos($result,$var2);

            if($dd == 0){

                $dd = strlen($result);

            }

        return substr($result,0,$dd);
    }
        

    /** 
    * ================================================================================= 
    * ---------------------------------- ENVIAR EMAIL --------------------------------- 
    * ================================================================================= 
    */
        
    public static function emailEnviar($email_destinatario, $assunto, $conteudo)
    {
        
        $Config = ConfigService::GetConfiguracoes();
        
        /*=========== INICIALIZA O OBJETO QUE ENVIA O EMAIL =======================================*/
        $transport = Swift_SmtpTransport::newInstance($Config['Email']['Envio']['smtp'], $Config['Email']['Envio']['smtpPorta']); //$transport = Swift_SmtpTransport::newInstance('smtp.web2business.com.br', 25);
        $transport->setUsername($Config['Email']['Envio']['login']);
        $transport->setPassword($Config['Email']['Envio']['senha']);
            
        $message = Swift_Message::newInstance();
        $message->setSubject($assunto);
        $message->setFrom(array($Config['Email']['Envio']['emailRemetente'] => $Config['Email']['Envio']['nomeRemetente']));
        //$message->setReturnPath('fabio@web2business.com.br');
            
        $mailer = Swift_Mailer::newInstance($transport);
        /*==============================================================================================*/
            
        $message->setBody($conteudo, 'text/html');
        $message->setTo(array($email_destinatario)); //não precisa limpar o destinatario a cada envio, esta função sobre-escreve o destinatario anterior
        //$message->setTo(array('fabio@web2business.com.br'));

        $mailer->send($message); 

    }


    /** 
    * ================================================================================= 
    * -------------------------------- GERARDOR DE SENHA ------------------------------ 
    * ================================================================================= 
    */

    public static function GeradorSenha($tipo="L N L N L N L N") 
    {
        // o explode retira os espaços presentes entre as letras (L) e números (N)        
        $tipo = explode(" ", $tipo);

        // Criação de um padrão de letras e números (no meu caso, usei letras maiúsculas
        // mas você pode intercalar maiusculas e minusculas, ou adaptar ao seu modo.)
        $padrao_letras = "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|X|W|Y|Z";
        $padrao_numeros = "0|1|2|3|4|5|6|7|8|9";

        // criando os arrays, que armazenarão letras e números
        // o explode retire os separadores | para utilizar as letras e números
        $array_letras = explode("|", $padrao_letras);
        $array_numeros = explode("|", $padrao_numeros);

        // cria a senha baseado nas informações da função (L para letras e N para números)
        $senha = "";

        for ($i=0; $i<sizeOf($tipo); $i++) {

            if ($tipo[$i] == "L") {

                $senha.= $array_letras[array_rand($array_letras,1)];

            } else {

                if ($tipo[$i] == "N") {

                    $senha.= $array_numeros[array_rand($array_numeros,1)];
                }

            }
        }
        // informa qual foi a senha gerada para o usuário naquele momento
        //echo "Senha: " . $senha,"<br>";
        //echo "Senha criptografada: " . md5($senha);
        return $senha;
    }

    /** 
    * ================================================================================= 
    * --------------------------------- FORMAT DATE DB -------------------------------- 
    * ================================================================================= 
    */ 

    public static function FormatDateDB($date)
    {
        $dt = explode('/', $date);
        return $newDate = $dt[2].'-'.$dt[1].'-'.$dt[0];
    }
    
    public static function FormatDateView($date)
    {
        $dt = explode('-', $date);
        return $newDate = $dt[2].'/'.$dt[1].'/'.$dt[0];
    } 


    /** 
    * ================================================================================= 
    * --------------------------------- FORMAT NUMBER --------------------------------- 
    * ================================================================================= 
    */ 

    public static function FormatNumberDb($number)
    {
        $retorno = number_format($number, 2, '.', '');
        return $retorno;
    }
    
    public static function FormatNumber($number)
    {
        $retorno = number_format($number, 2, ',', '');
        return $retorno;
    } 


    /** 
    * ================================================================================= 
    * ---------------------- EXIBIR PARTE DE UMA STRING COM ... -----------------------
    * ================================================================================= 
    */ 
    
    public static function LimitString($texto, $limite, $quebra = true){

        $tamanho = strlen($texto);

        if($tamanho <= $limite){ //Verifica se o tamanho do texto é menor ou igual ao limite

            $novo_texto = $texto;

        }else{ // Se o tamanho do texto for maior que o limite

            if($quebra == true){ // Verifica a opção de quebrar o texto

                $novo_texto = trim(substr($texto, 0, $limite))."...";

            }else{ // Se não, corta $texto na última palavra antes do limite

                $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite

                $novo_texto = trim(substr($texto, 0, $ultimo_espaco))."..."; // Corta o $texto até a posição localizada

            }

        }

    return $novo_texto; // Retorna o valor formatado

    }



    /** 
    * ================================================================================= 
    * ------------------------------------ LOG ERRO ----------------------------------- 
    * ================================================================================= 
    */ 

    public static function LogErro($MsgErro, $email = false)
    {

        (!empty($_SESSION['Email'])) ? $usuario = $_SESSION['Email'] : $usuario = $email; 

            $LogErro = array('Ip' => $_SERVER['REMOTE_ADDR'],'Usuario' => $usuario,'Controller'=> $_GET["Controller"],'Action'=> $_GET["Action"], 'MsgErro'=>$MsgErro);        

            $this->db->query_insert('logerro', $LogErro);
        
    }


} /* Fim da Classe */
?>