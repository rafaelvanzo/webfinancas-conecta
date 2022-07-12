<?php
$clienteId = $_GET['clienteId'];
$v_clientes = $db->fetch_assoc('select distinct id from clientes where cliente_id ='.$clienteId.' order by id DESC');

// Verifica se o cliente é desse contador
if($v_clientes == false){ echo "<script> location.href='clientes';</script>"; }
	
$clientes_dados = $db_w2b->fetch_assoc('select * from clientes where id ='.$clienteId);

$conectado = $db->fetch_assoc('select distinct conectado from clientes where cliente_id ='.$clienteId.' order by id DESC');
if($conectado['conectado'] == 1){ $conexao = "Conectado"; $cor = "green"; }else{ $conexao = "Desconectado"; $cor = "red"; };

$db_wf = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

$clientes_db = $db_wf->fetch_assoc('select db, db_senha from clientes_db where cliente_id ='.$clienteId.' and contador = 0');
$db_cliente_conexao = new Database('mysql.webfinancas.com',$clientes_db['db'],$clientes_db['db_senha'],$clientes_db['db']);

?>

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2><?php echo $clientes_dados['nome']; ?></h2>
                <!--<span style="padding-top:15px;font-size:14px;">teste</span> -->
            </div>

	     </div>
    </div>    
    <!-- Fim título -->  
      
	  <div class="wrapper">
      <!--
      <span class="line">
      </span>
      -->
      <div class="divider">
      	<span></span>
      </div>
    </div>
     
    <!-- main content wrapper -->
    <div class="wrapper">
    
         <!-- Notifications 
        <div class="nNote nWarning hideit" style="display:none;">
            <p></p>
        </div>
        <div class="nNote nInformation hideit" style="display:none;">
            <p></p>
        </div>   
        <div class="nNote nSuccess hideit" style="display:none;">
            <p></p>
        </div>  
        <div class="nNote nFailure hideit" style="display:none;">
            <p></p>
        </div>-->
    		
  
        <!-- =================== Palco =================== -->
           
 <!-- Organiza o layout -->   
 <div class="fluid">  

    <div class="span12">
    
    <br />
    
    <div class="controlB" >
            	<ul>
                	<!-- <li style="min-width:160px;"><a href="#" title=""><img src="<?php echo $raiz;?>images/icons/big/green/client.png" alt=""><span>Dados do cliente</span></a></li> -->
                	<li style="min-width:160px;"><a href="#" title=""><img src="<?php echo $raiz;?>images/icons/big/green/config.png" alt=""><span>Configuração</span></a></li>
                    <li style="min-width:160px;"><a href="#" title=""><img src="<?php echo $raiz;?>images/icons/big/blue/documents.png" alt=""><span>Gerenciar arquivos</span></a></li>
                    <li style="min-width:160px;"><a href="#" title=""><img src="<?php echo $raiz;?>images/icons/big/blue/clipboard.png" alt=""><span>Gerar arquivo contábil</span></a></li>
                </ul>
            </div>
    
    
   <!--      <div class="widget" style="height:400px;" align="center">
                  <div class="title tipS" original-title="Mensagens não lidas" ><img src="<?php echo $raiz;?>images/icons/dark/bubbles.png" alt="" class="titleIcon"><h6>Conversas</h6>
                  <div class="topIcons">
                  			<a href="#" title="" class="button blueB" style="margin: -8px;"><img src="<?php echo $raiz;?>images/icons/light/bubbles.png" alt="" class="icon"><span>Nova conversa</span></a> <!-- speech.png -->
                   <!--      </div>                  
                  </div>
                 		<div class="scroll" style="height:360px;">
                        <ul class="partners">
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Dave Armstrong</strong></a>
                                    <i>Creative director at Google Inc. Zurich</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                    <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Nora McDonald</strong></a>
                                    <i>Lead developer, Alaska</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Natalie Zimmerman</strong></a>
                                    <i>Actually it's a guy. Yeah, unexpected</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                             <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                             <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                             <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                        </ul>
                    </div>
               </div>  
       </div> -->


 </div> <!-- Fim Fluid bloco -->


	<div class="fluid">
    
    <div class="widget" style="height:400px;" align="center">
                  <div class="title tipS" original-title="Mensagens não lidas" ><img src="<?php echo $raiz;?>images/icons/dark/bubbles.png" alt="" class="titleIcon"><h6>Conversas</h6>
                  <div class="topIcons">
                  			<a href="#" title="" class="button blueB" style="margin: -8px;"><img src="<?php echo $raiz;?>images/icons/light/bubbles.png" alt="" class="icon"><span>Nova conversa</span></a> <!-- speech.png -->
                         </div>                  
                  </div>
                 		<div class="scroll" style="height:360px;">
                        <ul class="partners">
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Dave Armstrong</strong></a>
                                    <i>Creative director at Google Inc. Zurich</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                    <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Nora McDonald</strong></a>
                                    <i>Lead developer, Alaska</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Natalie Zimmerman</strong></a>
                                    <i>Actually it's a guy. Yeah, unexpected</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                            <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                             <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                             <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                             <li>
                                <a href="#" title="" class="floatL"><img src="<?php echo $raiz;?>images/user.png" alt=""></a>
                                <div class="pInfo" align="left">
                                    <a href="#" title=""><strong>Maria Paradeux</strong></a>
                                    <i>Very hot secretary, Playboy rockstar</i>	
                                </div>
                                <div class="pLinks">
                                    <a href="#" class="tipW" original-title="Direct call"><img src="<?php echo $raiz;?>images/icons/pSkype.png" alt=""></a>
                                    <a href="#" class="tipW" original-title="Send an email"><img src="<?php echo $raiz;?>images/icons/pEmail.png" alt=""></a>
                                </div>
                            </li>
                        </ul>
                    </div>
               </div>  
       </div>
    
    </div>

 </div> <!-- Fim Fluid Tudo --> 
    
    
 
 <!-- ====== Fim do Palco ====== -->
  
	</div> 
</div>