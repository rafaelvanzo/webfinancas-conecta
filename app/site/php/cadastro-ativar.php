<?php
// ====================== db ======================= 
require('Database.class.php');
require ('php-mailer/class.phpmailer.php');

$host = "mysql.web2business.com.br";
$usuario = "web2business";
$senha = "W2BSISTEMAS";
$db_usuario = "web2business";
$db_w2b = new Database($host,$usuario,$senha,$db_usuario);

$host = "mysql.webfinancas.com";
$usuario = "webfinancas";
$senha = "W2BSISTEMAS";
$db_usuario = "webfinancas";
$db = new Database($host,$usuario,$senha,$db_usuario);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>E-mail Web Finanças</title>

	<style>
  /* ========================== CSS ============================ */
  body { 
  padding:0 !important; 
  margin:0 !important; 
  display:block !important; 
  background:#f8f8f8;  
  font-family: Arial, Helvetica, sans-serif;
  font-size:12px;
  color:#666666;
  }
  a { text-decoration: none; }
  
  #palco {
  border: 0px solid #CCC;
  min-width: 250px;
  max-width: 700px;
  min-height: 200px;
  height: 100%;
  margin-left:auto;
  margin-right:auto;
  border-radius: 20px;
  }
  
  #rodape {
  text-align: left;
  min-width: 250px;
  max-width: 700px;
  height: 50px;
  margin-left:auto;
  margin-right:auto;
  font-size:10px;
  }
  
  .img { max-width: 350px; }
  .bt { background: #007CC3; /* background:#289CDC;*/ padding:10px 14px;-webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; color: #FFF; text-decoration: none; font-weight: bold;}
  
  /* Social Icons */
  ul.social-icons {
    margin: 0;
    padding: 0;
    width: auto;
  }
  
  ul.social-icons li {
    background-image: url(https://www.webfinancas.com/site/img/email_paginas/social-sprites.png);
    background-repeat: no-repeat;
    background-color: #FFF;
    background-position: 0 100px;
    display: inline-block;
    margin: -1px 1px 5px 0;
    padding: 0;
    border-radius: 100%;
    overflow: visible;
    transition: all 0.3s ease;
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.3);
    -moz-border-radius: 100%;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    -webkit-border-radius: 100%;
    -webkit-transition: all 0.3s ease;
  }
  
  ul.social-icons li a {
    display: block;
    height: 30px;
    width: 30px;
    text-align: center;
  }
  
  ul.social-icons li[class] a {
    text-indent: -9999px;
  }
  
  ul.social-icons li a:hover {
    text-decoration: none;
  }
  
  ul.social-icons li a i[class^="icon-"] {
    color: #444;
    font-style: 16px;
    position: relative;
    top: 3px;
  }
  
  ul.social-icons li a:active {
    box-shadow: inset 0 0 10px rgba(0,0,0,0.3), inset 0 0 10px rgba(0,0,0,0.3);
    -moz-box-shadow: inset 0 0 10px rgba(0,0,0,0.3), inset 0 0 10px rgba(0,0,0,0.3);
    -webkit-box-shadow: inset 0 0 10px rgba(0,0,0,0.3), inset 0 0 10px rgba(0,0,0,0.3);
  }
  
  ul.social-icons li:active,
  ul.social-icons li a:active {
    border-radius: 100%;
    -moz-border-radius: 100%;
    -webkit-border-radius: 100%;
  }
  
  ul.social-icons li.facebook {
    background-position: 0 -120px;
  }
  
  ul.social-icons li.facebook:hover {
    background-position: 0 -150px;
  }
  
  ul.social-icons li.googleplus {
    background-position: 0 -300px;
  }
  
  ul.social-icons li.googleplus:hover {
    background-position: 0 -330px;
  }
  
  ul.social-icons li.linkedin {
    background-position: 0 -540px;
  }
  
  ul.social-icons li.linkedin:hover {
    background-position: 0 -570px;
  }
  
  </style>

</head>

<body>

  <br/>
  
  <div id="palco">
  
  <?php
	$link = mysqli_connect('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');
	$query_usuario = mysqli_query($link,'select cliente_id, situacao, email from usuarios where id = '.$_GET['id']);
	$usuario = mysqli_fetch_assoc($query_usuario);
	if($usuario['situacao']==0){
		mysqli_query($link,'update usuarios set situacao = 1 where id = '.$_GET['id']);
		$dt_cadastro = date('Y-m-d H:i:s');
		mysqli_query($link,'insert into clientes_trial(cliente_id,dt_cadastro) values('.$usuario['cliente_id'].',"'.$dt_cadastro.'")');
			
			
			//===================== CONVITE =======================
			 
			//Pega token com as váriaveis do convite $token = $remetente_id.'.'.$id_list.'.'.$cliente_id;
			$t = $_GET['token'];
			$token = explode(".", $t);
		
			$remetente_id = $token['0'];	
		
		//Verifica se o link veio de um convite
		if(!empty($remetente_id)){			
			
			$id_list = $token['1']; //id da lista de convites do remetente
			$cliente_id = $token['2']; //id do usuario no db web2business destinatario
			$email =  $_GET['email']; //email de cadastro do cliente
			$dt_inicio = date('Y-m-d H:m:s');
		
		//Pega o email do remetente (Contador ou Cliente)
		$remetente_email = $db_w2b->fetch_assoc('select email from clientes where id = "'.$remetente_id.'"');
		
		//Busca os dados de conexão com o db do remetente
		$db_dados_remetente = $db->fetch_assoc('select db, db_senha from clientes_db where cliente_id ='.$remetente_id);
		
			//Conecta no db do remetente
			$db_dados_remetente = new Database("mysql.webfinancas.com",$db_dados_remetente['db'],$db_dados_remetente['db_senha'],$db_dados_remetente['db']);
		
		//dados do destinatario no db do remetente
		$busca_dados_destinatario = $db_dados_remetente->fetch_assoc('select dt_convite from clientes where id ='.$id_list);
		$dt_convite = $busca_dados_destinatario['dt_convite'];
		
		$dados_remetente = array('email'=> $email,'cliente_id'=> $cliente_id, 'dt_inicio' => $dt_inicio,'conectado' => '1');
		
		//Atualiza os dados do convite dentro do db do Remetente
		$db_dados_remetente->query_update('clientes',$dados_remetente, 'id ='.$id_list);
			
		//----------------- Destinatario -------------------//

			//Busca os dados de conexão com o db do destinatario
			$db_wf_dest_dados = $db->fetch_assoc('select db, db_senha from clientes_db where cliente_id = '.$cliente_id);
		
			//Conecta no db do destinatario
			$db_wf_dest = new Database("mysql.webfinancas.com",$db_wf_dest_dados['db'],$db_wf_dest_dados['db_senha'],$db_wf_dest_dados['db']);
	
			// Destinatario inserindo convite		
			$db_wf_dest->query("insert into contador (dt_convite, email, contador_id, dt_inicio, conectado) values ('".$dt_convite."', '".$remetente_email['email']."', '".$remetente_id."', '".$dt_inicio."', 1)");
		
		$db_dados_remetente->close();		
		$db_wf_dest->close();
		
		}
	
			
			
	}
	$affected_rows = mysqli_affected_rows($link);
	if($affected_rows>0 || $usuario['situacao']==1){
	?>

    <table width="100%" height="100%">
        <tr>
        
          <td width="50%" align="center"><img src="https://www.webfinancas.com/site/img/logo_webfinancas_fundo_branco.png" align="middle" class="logo" /> </td>      
          <td width="50%" align="center">
              <div class="social-icons">
                 <ul class="social-icons">
                    <li class="facebook"><a href="http://www.facebook.com/webfinancas" target="_blank" title="Facebook">Facebook</a></li>
                    <li class="googleplus"><a href="http://www.googleplus.com/webfinancas" target="_blank" title="Twitter">Twitter</a></li>
                    <li class="linkedin"><a href="http://www.linkedin.com/webfinancas" target="_blank" title="Linkedin">Linkedin</a></li>
                  </ul> 
                </div>
          </td>
          
        </tr>
        <tr>
        
        <td width="50%" rowspan="2" align="center" valign="top">  
        
         <br /> <br /> <br />
        
          <h2> Conta ativada com sucesso! </h2>
          <p>Agora você pode começar a utilizar os <br /> benefícios do Web Finanças. <br /> </p> <br />
          <p align="center"><a href="https://www.webfinancas.com/#login" class="bt"> Entrar no Web Finanças</a></p>
        </td>    
        <td width="50%" align="center" valign="bottom"> 
           <img src="https://www.webfinancas.com/site/img/email_paginas/slide1.png" align="right" class="img" />
        </td>
         
      </tr>
    </table>

	<?php
    }else{
  ?>    

    <table width="100%" height="100%">
        <tr>
        
          <td width="50%" align="center"><img src="https://www.webfinancas.com/site/img/logo_webfinancas_fundo_branco.png" align="middle" class="logo" /> </td>      
          <td width="50%" align="center">
              <div class="social-icons">
                 <ul class="social-icons">
                    <li class="facebook"><a href="http://www.facebook.com/webfinancas" target="_blank" title="Facebook">Facebook</a></li>
                    <li class="googleplus"><a href="http://www.googleplus.com/webfinancas" target="_blank" title="Twitter">Twitter</a></li>
                    <li class="linkedin"><a href="http://www.linkedin.com/webfinancas" target="_blank" title="Linkedin">Linkedin</a></li>
                  </ul> 
                </div>
          </td>
          
        </tr>
        <tr>
        
        <td width="50%" rowspan="2" align="center" valign="top">
        
         <br /><br />
        
          <h2> Usuário inexistente! </h2>
          <p>Após o cadastro, a confirmação deve ser feita em até 24H. </p>
          <p>Por favor, realize um novo cadastro no site do Web Finanças. </p> <br />
          <p align="center"><a href="https://www.webfinancas.com/#cadastro" class="bt"> Efetuar cadastro agora </a></p>
        </td>    
        <td width="50%" align="center" valign="bottom"> 
        <br />
           <img src="https://www.webfinancas.com/site/img/email_paginas/aviso.png" align="center" width="256" />	
         </td>
         
      </tr>
    </table>

  <?php
    }
  ?>    
  
  </div>
  
  <div id="rodape">
  
    <div style="font-size:0pt; line-height:0pt; height:1px; background:#e3eaf0;"></div>
    <div style="font-size:0pt; line-height:0pt; height:1px; background:#ffffff;"></div>
  
    <p align="center">
   	<!-- *Caso não seja o seu e-mail em 24h os registros cadastrados em nossos sistemas serão apagados.<br /> -->
    Para maiores informações acesse o site <span style="color:#289CDC;"><a href="http://www.webfinancas.com" target="_blank"><b>www.webfinancas.com</b></a></span>.
    </p>
  
  </div>

</body>
</html>


