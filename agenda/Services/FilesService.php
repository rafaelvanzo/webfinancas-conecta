<?php
/**
 * Class para manipular arquivos e imagens no WEBEDIT/MODULI
 * 
 * @version 1.0
 * @author Rafael Vanzo
 * @Services
 */

class FilesService
{

    private $db;

    function __construct($db)
    {

        $this->db = $db;

    }

/** 
* ================================================================================= 
* ------------------------------------- CREATE ------------------------------------ 
* ================================================================================= 
*/ 

public function Create($pasta, $name, $tabela, $registroId, $Principal)
{

    try{ 

        $dados['Pasta'] = $pasta;

        $dados['Arquivo'] = $name;

        $dados['Tabela'] = $tabela;

        $dados['Tabela_registro_id'] = $registroId;

        $dados['Principal'] = $Principal;

        
            $id = $this->db->query_insert('arquivos', $dados);


        return $id;


    }catch(Exception $e){

            
        $this->db->query('rollback');

        parent::LogErro($e->getMessage());        
        
        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */        

    }

        $this->db->close();

}

 
 
/** 
* ================================================================================= 
* -------------------------------------- EDIT ------------------------------------- 
* ================================================================================= 
*/ 

public function Edit($pasta, $name, $tabela, $registroId, $Principal = false)
{

    try{    

        $dados['Pasta'] = $pasta;

        $dados['Arquivo'] = $name;

        $dados['Tabela'] = $tabela;

    
        if( $Principal != false && $Principal > 0 )
        {		
            
            $SqlPrincipal = ' AND Principal = "'.$Principal.'"';		
        
        }

        //Remove a imagem anterior
        $arquivoAntigo = $this->db->fetch_assoc('SELECT Pasta, Arquivo FROM arquivos WHERE Tabela = "'.$tabela.'" AND Tabela_registro_id = '.$registroId.$SqlPrincipal);

        self::Delete($arquivoAntigo['Pasta'], $arquivoAntigo['Arquivo']);       
        
            $id = $this->db->query_update('arquivos', $dados, 'Tabela = "'.$tabela.'" AND Tabela_registro_id = '.$registroId.$SqlPrincipal);


    return $id;


    }catch(Exception $e){

            
        $this->db->query('rollback');

        parent::LogErro($e->getMessage());         

        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */
        
    }

        $this->db->close();

}



/** 
* ================================================================================= 
* ------------------------------------- DELETE ------------------------------------ 
* ================================================================================= 
*/ 

public function Delete($Caminho, $Documento)
{
       
    try{
        
        //verifica se a váriavel esta preenchida.
        if(!empty($Documento)){


        if(file_exists($Caminho.'/'.$Documento)){

            //Remove o arquivo
            unlink($Caminho.'/'.$Documento);

            //Exclui o Thumb se existir
            $ext = strrchr($Documento, '.');

            if($ext == '.png' || $ext == '.jpg' || $ext == '.jpeg' || $ext == '.gif')
            { 

                if(file_exists($Caminho.'/Thumbs/thumb-'.$Documento))
                { 
                
                    unlink($Caminho.'/Thumbs/thumb-'.$Documento); 
                
                }

            } 

            return true;
            }
            

        }
    
    }catch(Exception $e){                                   
    
        $this->db->query('rollback');
    
        parent::LogErro($e->getMessage());                     

        /* Obs.: Para testar se o Exception está funcionando insira o seguinte código dentro do Try: throw new Exception("Some error message"); */
    
    }

}



/**
* ================================================================================= 
* -------------------------------- UPLOADS DROPZONE -------------------------------  
* =================================================================================  
*/
public function Uploads($Pasta, $larguraThumb = false)
{  

try{ 
       

    if (!empty($_FILES['file'])) { 


        $tempFile = $_FILES['file']['tmp_name']; 

        $targetPath = realpath(dirname( __FILE__ ) . '/../Uploads');  

            
            $OldName = $_FILES['file']['name'];

            $ext = strrchr($OldName, '.');

            $NovoNome = 'arq-'.uniqid().$ext; 


            $targetFile =  $targetPath.'/'. $NovoNome;  

            move_uploaded_file($tempFile,$targetFile);           
                
                
                //Verifica se o arquivo é uma imagem e cria o thumb
                $ext = strtolower($ext);

                if($ext == '.png' || $ext == '.gif'  || $ext == '.jpg' || $ext == '.jpeg' ){ 

                    //Thumb image - Informe apenas a largura que gostaria que o Thumb divesse
                    self::ThumbImage($targetPath, $NovoNome, $targetPath.'/Thumbs', $larguraThumb);

                }

            
                //Envio de arquivo via CURL
                //self::envioArquivoCurl('http://www.adcos.com.br/FrontEndServer/Arquivos/Receber', $targetPath.'/'.$NovoNome, $_FILES['file']['type'], $NovoNome, '/Uploads/');               

            
                return $NovoNome; // Retornar para o DropZone o nome do arquivo.

        }


    }catch(Exception $e){                                   
            
        $this->db->query('rollback');
        
        parent::LogErro($e->getMessage());                    
        
    }

}


public function UploadsCreate($dzNome, $pasta, $id, $tabela)
{

    try{

        $DZimagem = explode(' ', $dzNome); $i = 0; 

        while($i <= count($DZimagem)){   

            if(!empty($DZimagem[$i])){   

                $ArquivoId = Self::Create($pasta, $DZimagem[$i], $tabela, $id, '0'); 
                
                //===== ACESSIBILIDADE: Prepara o name da legenda para localizar no array o valor.
                //$Legenda = explode(".", $DZimagem[$i]);
                
                // ===== Legenda para acessibilidade =====
                //$this->db->query_update('arquivos', array('Legenda' => $dadosPost2[$Legenda[0]]), 'Id ='.$ArquivoId);                    

                }   $i+=1;  

        }
        
        

    }catch(Exception $e){                                   
            
        $this->db->query('rollback');
        
        parent::LogErro($e->getMessage());                    
        
    }
}



public function UploadsEdit($dzNome, $pasta, $id, $tabela)
{

    try{

        //Remove os espaços do array que o DropZone envia
        $DZimagem = explode(' ', $dzNome);

        //Exclui todas as imagens
        $imgAtualizar = $this->db->fetch_all_array('SELECT Id, Arquivo FROM arquivos WHERE Principal = 0 AND Tabela = "'.$tabela.'" AND tabela_registro_id ='.$id);
        
        foreach($imgAtualizar as $imgAtualizar){ 

            $this->db->query('DELETE FROM arquivos WHERE Id ='.$imgAtualizar['Id']); 

                if(!in_array($imgAtualizar['Arquivo'], $DZimagem)){
                    
                    self::Delete($pasta, $imgAtualizar['Arquivo']);

                }

        }

        //Inclui novamente todas as imagens
        $i = 0;  
        while($i <= count($DZimagem)){       

            if(!empty($DZimagem[$i])){     

                $ArquivoId = self::Create($pasta, $DZimagem[$i], $tabela, $id, '0'); 

                //===== ACESSIBILIDADE: Prepara o name da legenda para localizar no array o valor.
                //$Legenda = explode(".", $DZimagem[$i]);
                
                // ===== Legenda para acessibilidade =====
                //$this->db->query_update('arquivos', array('Legenda' => $dadosPost2[$Legenda[0]]), 'Id ='.$ArquivoId); 

            }   $i+=1; 

        }

        

    }catch(Exception $e){                                   
            
        $this->db->query('rollback');
        
        parent::LogErro($e->getMessage());                    
        
    }
}


public function SummerNoteImg($pasta)
{    

            $tempFile = $_FILES['file']['tmp_name'];      

            $targetPath = realpath(dirname( __FILE__ ) . $pasta);  

            
            $OldName = $_FILES['file']['name'];

            $ext = strrchr($OldName, '.');

            $NovoNome = 'arq-'.uniqid().$ext; 
                
                $targetFile =  $targetPath . '/' . $NovoNome; 

                move_uploaded_file($tempFile,$targetFile);             

            return 'Uploads/Editor/'.$NovoNome;
}
 

/** 
* ================================================================================= 
* ----------------------------------- SLIM UPLOAD --------------------------------- 
* ================================================================================= 
*/ 
public function SlimUpload($Params, $RegistrId, $Tp, $larguraThumb = false)
{
    
try{ 
        
        $SlimPOST =  $Params;   

        $SlimQtd = count($SlimPOST);
        
        
            for ($i = 1; $i <= $SlimQtd; $i++) {	

                $SlimImage = json_decode($SlimPOST[$i], true); 

                $Imagem = $SlimImage['output']['image'];

                $Arquivo = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $Imagem));

                
                $targetPath = realpath(dirname( __FILE__ ) . '/../Uploads'); 

                $OldName = $SlimImage['output']['name'];

                $ext = strrchr($OldName, '.');

                $NovoNome = 'arq-'.uniqid().$ext; 

                
                if(!empty($OldName)){
                
                    
                    file_put_contents($targetPath.'/'.$NovoNome, $Arquivo);
                    
                    self::ThumbImage($targetPath, $NovoNome, $targetPath.'/Thumbs', $larguraThumb);
                    
                    $Controller = $_GET['Controller'];	

                    $name = $NovoNome;

                    if($Tp == 'C'){

                        self::Create('Uploads', $name, $Controller, $RegistrId, $i);     //Maior que 0, significa que é a imagem do slimCropper
                    
                    }elseif($Tp == 'E'){
                        
                        self::Edit('Uploads', $name, $Controller, $RegistrId, $i);
                    
                    }


                }else{
                
                    $name = 'sem_imagem.png';

                }
                            
                    $retorno[$i] = $name;
            
            }   //Fim FOR
        
    
    
    return $retorno;								//retorna o array('0'=> primeira imagem, '1' => segunda_image);
    


    }catch(Exception $e){                                   
        
    $this->db->query('rollback');
    
    //parent::LogErro($e->getMessage());                     
    
    }

}


/**
* ================================================================================= 
* ------------------------------ REDIMENCIONAR IMAGENS ----------------------------  
* =================================================================================  
*/    

private function ThumbImage($targetPath, $imagem, $targetThumb, $largura)
{   
    
        $largura = $largura ? $largura : 300;
    
            // Verifica extensão do arquivo
            $extensao = strrchr($imagem, '.');
            
            $extensao = strtolower($extensao);
    
    
            switch($extensao) 
            {
                case '.png':
                
                     $funcao_cria_imagem = 'imagecreatefrompng';
                     
                     $funcao_salva_imagem = 'imagepng';
                                      
                     break;
                     
                case '.gif':
                    
                     $funcao_cria_imagem = 'imagecreatefromgif';
                     
                     $funcao_salva_imagem = 'imagegif';
                     
                     break;
                     
                 case '.jpg':
                 
                     $funcao_cria_imagem = 'imagecreatefromjpeg';
                     
                     $funcao_salva_imagem = 'imagejpeg';
    
                     break;
                     
                case '.jpeg':
    
                         $funcao_cria_imagem = 'imagecreatefromjpeg';
                         
                         $funcao_salva_imagem = 'imagejpeg';
                                
                        break;
    
                 default:
                 
                     return 'Erro. Tipo de arquivo não aceito';
                     
                     exit;
                     
                     
                     break;
             }
        
            
            $imagem_original = $funcao_cria_imagem($targetPath.'/'.$imagem);            
            
            list($largura_antiga, $altura_antiga) = getimagesize($targetPath.'/'.$imagem);            
            
            $largura;
            $altura = ($altura_antiga * $largura) / $largura_antiga; // nova largura
    
            
            $imagem_tmp = imagecreatetruecolor($largura, $altura);
    
                //Verifica se o arquivo PNG tem fundo transparente e cria a imagem.
                if($extensao == '.png'){
    
                    $im = imagecreatefrompng($targetPath.'/'.$imagem);
    
                        if(self::check_transparent($im)) {
    
                            //Cria a imagem png transparente
                            imagealphablending($imagem_tmp, false);
    
                            imagesavealpha($imagem_tmp, true);
    
                            $transparent = imagecolorallocatealpha($imagem_tmp, 255, 255, 255, 127);
    
                            imagefilledrectangle($imagem_tmp, 0, 0, $largura, $altura, $transparent);
    
                        }                
                }
    
            // Faz a interpolação da imagem base com a imagem original
            imagecopyresampled($imagem_tmp, $imagem_original, 0, 0, 0, 0, $largura, $altura, $largura_antiga, $altura_antiga);
        
            // Salva a nova imagem
            $resultado = $funcao_salva_imagem($imagem_tmp, $targetThumb."/thumb-$imagem");    
            
            imagedestroy($imagem_original);
            
            imagedestroy($imagem_tmp);
            
            
            if($resultado)
            {
    
                return true;
                
            }else{
    
                return false;
                
            }
        
    
}
    
/**
* ================================================================================= 
* --------------- Verifica se o arquivo PNG tem fundo transparente ----------------  
* =================================================================================  
*/  
private function check_transparent($im) 
{
    
        $width = imagesx($im); // Get the width of the image
        
        $height = imagesy($im); // Get the height of the image
    
    
        // We run the image pixel by pixel and as soon as we find a transparent pixel we stop and return true.
        for($i = 0; $i < $width; $i++) {
           
            for($j = 0; $j < $height; $j++) {
             
                $rgba = imagecolorat($im, $i, $j);
              
                if(($rgba & 0x7F000000) >> 24) {
                
                    return true;
    
                }
            }
        }
        
        // If we dont find any pixel the function will return false.
        return false;
    
}
    

} /* Fim Class */
?>
