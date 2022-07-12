<!-- Convite Contador -->

<div id="dialog-conversa" style="height: auto; padding:0; text-align: center; display:none;" title="Nova conversa">
  <form id="formAddConversa" action="#" class="dialog"> 
    <input type="hidden" name="funcao" id="funcao" value="" />
    <input type="hidden" name="chat_id" id="chat_id" value="" />
    <input type="hidden" name="cliente_id" value="<?php echo $_SESSION['cliente_id'];?>" />
    <?php $prestador_id = $db->fetch_assoc("SELECT cliente_id FROM conexao WHERE conectado = 1"); ?>
    <input type="hidden" name="prestador_id" value="<?php echo $prestador_id['cliente_id'];?>" />
    
    <div class="fluid">

    <div class="title MaisOpcoes closed inactive" align="center"> 
<a href="#" class="button buttonMaisOpcoes tipS" original-title="Clique para selecionar ou visualizar os participantes."><img src="images/icons/seta_baixo.png" alt="" class="iconMaisOpcoes"><span class="nomeP">Participantes</span></a>  </div>
  
      <div class="body participantes" style="display: block;"> <!-- Body Mais Opções -->
 <label style="margin-left:33px;"><input type="checkbox" name="chboxTodos" value="" checked="checked"> &nbsp; Selecionar todos  </label>
    <br /> 
    <!--=====================================-->
      <div class="formRow">
     
        <span class="span12 controlB" >
               
          <ul style="text-align:left; padding:2px;">
            <li style="width:175px; height:47px; border:0; font-weight:normal;   background:none;">
					<label> 
					<span class="floatL">
                    	<input type="checkbox" name="p1" value="1"checked="checked"> 							
						<img src="../sistema/images/user.png" alt="" class="floatL bordaRedonda" style="margin-left:-14px;margin-top:0px;" >
					  </span>
					   <span class="floatL" style="text-align:left; padding-left:4px;">
						<strong>Fabio L. Moreto</strong>
							<br>Setor Administrativo
					   </span>		
					 </label>
					
				</li>
                <li style="width:175px; height:47px; border:0; font-weight:normal;   background:none;">
					<label> 
					<span class="floatL">
                    	<input type="checkbox" name="p2" value="2"checked="checked"> 							
						<img src="../sistema/images/user.png" alt="" class="floatL bordaRedonda" style="margin-left:-14px;margin-top:0px;" >
					  </span>
					   <span class="floatL" style="text-align:left; padding-left:4px;">
						<strong>Fabio L. Moreto</strong>
							<br>Setor Administrativo
					   </span>		
					 </label>
					
				</li>
              </ul>
	                
    	  </span>

     <!--=====================================--> 
       </div>

  <div class="linha"></div>
    
    </div>
    
       <div class="formRow msgTodas scroll" style="height:250px;">
      
          <span class="span12">
             <ul class="messagesOne">
             
<?php
/*
$arquivo = fopen('https://www.webfinancas.com/conversas/chat_1.txt','r');
if ($arquivo != false){
//while(!feof($arquivo)){
		$c = 1;
while($c <= 5){
	$c++;	
$linha = fgets($arquivo);
$info = explode("|",$linha);
?>
                    <li class="by_user">
                        <img src="../sistema/images/user.png" class="floatL bordaRedonda" />
                        <div class="messageArea" align="left">
                            <span class="aro"></span>
                            <div class="infoRow">
                                <span class="name"><strong><?php echo "Rafael Vanzo";?></strong>:</span>
                                <span class="time"><?php echo $info[1];?></span>
                            </div>
                            <?php echo $info[2];?>
                        </div>
                    </li> 
                    
                        
<?php
	}
fclose($arquivo);
} */
?> 
                  <li id="new" class="by_me" style="display:none;"></li>

                </ul>

          </span>
       
       </div>

       
       <div class="linha"></div>
       
    <div class="formRow msgAssunto">

        <span class="span12">
            <input type="text" name="assunto" class="txtAssunto" placeholder="Assunto"/>
          </span>

    </div>
       
    <div class="formRow">

        <span class="span10">
            <textarea name="msg" rows="3" placeholder="Mensagem"></textarea>
          </span>
       <span class="span2" style="text-align:center;">   
        	<a href="javascript://" class="wContentButton bluewB" onClick="javascript:add_conversa();" style="color:#FFFFFF; ">Enviar</a>
        </span>

    </div>

    </div>  <!-- fluid --> 
  </form>
</div><!-- Fim dialog --> 

<!-- Convite Contador -->