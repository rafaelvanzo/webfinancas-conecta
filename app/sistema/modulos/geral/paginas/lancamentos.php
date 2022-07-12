<!-- <script> alert(window.innerWidth); </script> --> 
 
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Lançamento</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php include("cambio/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs -->
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="#">Geral</a>
	               </li>
                 <li class="current">
                      <a href="#">Lançamentos</a>
                 </li>
            </ul>
	</div>  
</div> <!-- Fim Breadcrumbs -->

    <br />
   
    <!-- Botões -->
    <div class="wrapper">        	    
      <a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-rcbt-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo recebimento</span></a>
      <a href="#" title="" class="button redB" style="margin: 5px;" id="opener-pgto-incluir"><img src="images/icons/light/postcard.png" alt="" class="icon"/><span>Novo pagamento</span></a>
      <a href="#" title="" class="button blueB" style="margin: 5px;" id="opener-trans-incluir"><img src="images/icons/light/transfer.png" alt="" class="icon"/><span>Nova transferência</span></a>
    </div>

    
 <!--   <div class="line"></div>
    
    <!-- Main content wrapper -->
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
   
 	 <!-- Contas -->

   <?php
	 $array_contas = $db->fetch_all_array("select c.id, b.nome, c.descricao, c.vl_saldo, c.vl_credito from contas c, bancos b where c.banco_id = b.id order by b.nome");
	 ?>

    <div class="span4">
    
   	<div class="widget">
    
                <div class="title"><img src="images/icons/dark/money.png" alt="" class="titleIcon"><h6>Saldo(s)</h6> <div class="num"><a href="#" class="blueNum"><?php echo count($array_contas)?></a></div> </div>
                <div class="scroll" style="height:136px;">
										 <input type="hidden" id="conta_id" value="<?php echo $array_contas[0]['id'];?>" /> <!-- armazena a id da conta que está sendo exibida na página de lançamentos(usado pela função de editar e de excluir) -->
                     <div class="newOrder" id="contasSaldo">

                    	 <?php
											 $saldo_total = $array_contas[0]['vl_saldo']+$array_contas[0]['vl_credito'];
											 $nome_conta_ini = $array_contas[0]['nome']." - ".$array_contas[0]['descricao'];
											 $conta_id_ini = $array_contas[0]['id'];

											 	echo '
													<div class="userRow">
															<img src="images/bank.png" alt="" class="floatL">
															<ul class="leftList">
																	<li><a href="#" title="" onClick="lancamentosListar(\''.$array_contas[0]['id'].'\')"><strong>'.$array_contas[0]['nome'].'</strong></a></li>
																	<li>'.$array_contas[0]['descricao'].'</li>
															</ul>
															<div class="rightList"><img src="images/icons/icon-checked-conta.png" border="0"/></div>
													</div>
													<div class="orderRow">
															<ul class="leftList">
																	<li>Saldo:</li>
																	<li>C. Especial:</li>
																	<li><b>Total:</b></li>
															</ul>
															<ul class="rightList">
																	<li>R$ '.number_format($array_contas[0]['vl_saldo'],2,',','.').'</li>
																	<li>R$ '.number_format($array_contas[0]['vl_credito'],2,',','.').'</li>
																	<li><strong class="grey">R$ '.number_format($array_contas[0]['vl_saldo']+$array_contas[0]['vl_credito'],2,',','.').'</strong></li>
															</ul>
													</div>
													<div class="cLine"></div>
												';

											 unset($array_contas[0]);

											 foreach($array_contas as $conta){
											 	echo '
													<div class="userRow">
															<img src="images/bank.png" alt="" class="floatL">
															<ul class="leftList">
																	<li><a href="#" title="" onClick="lancamentosListar(\''.$conta['id'].'\')"><strong>'.$conta['nome'].'</strong></a></li>
																	<li>'.$conta['descricao'].'</li>
															</ul>
															<div class="rightList"></div>
													</div>
													<div class="orderRow">
															<ul class="leftList">
																	<li>Saldo:</li>
																	<li>C. Especial:</li>
																	<li><b>Total:</b></li>
															</ul>
															<ul class="rightList">
																	<li>R$ '.number_format($conta['vl_saldo'],2,',','.').'</li>
																	<li>R$ '.number_format($conta['vl_credito'],2,',','.').'</li>
																	<li><strong class="grey">R$ '.number_format($conta['vl_saldo']+$conta['vl_credito'],2,',','.').'</strong></li>
															</ul>
													</div>
													<div class="cLine"></div>
												';
												$saldo_total += $conta['vl_saldo']+$conta['vl_credito'];
											 }
											 ?>
                       
                    </div>
                </div> 

              	<div class="cLine"></div>

                <div class="totalAmount"><h6 class="floatL blue">Total:</h6><h6 class="floatR blue" id="saldoTotal">R$ <?php echo number_format($saldo_total,2,',','.')?></h6></div>

             </div>

		   </div>  <!-- Fim Contas -->   

    <div class="span8"> <!-- Buscar período -->
    
            <div class="widget">
               
               <div class="title"><img src="images/icons/dark/magnify.png" alt="" class="titleIcon" /><h6>Pesquisar</h6></div>
               
               <div class="formRow fluid">
                 <form action="" class="form" id="formBuscarPeriodo">
                   <input type="hidden" name="funcao" value="lancamentosBuscarPeriodo">
                 	 <input type="hidden" class="conta_id" name="conta_id" value="<?php echo $conta_id_ini?>">
                   <input type="hidden" name="tp_busca" value="periodo">
                   <label><strong>Selecione o Período:</strong></label>
                   <div class="formRight">
                     De: <input name="dt_ini" type="text" class="datepicker" />
                     &nbsp;
                     Até: <input name="dt_fim" type="text" class="datepicker" />
                     &nbsp;&nbsp;&nbsp;<a href="javascript://void(0);" title="" class="button greenB" onClick="lancamentosBuscarPeriodo();"><img src="images/icons/light/magnify.png" alt="" class="icon"><span>Buscar</span></a>
                   </div>
                 </form>
               </div>
                
                <div class="formRow fluid">
                  <label><strong>Selecione o Mês:</strong></label>

                  <form action="" class="form" id="formBuscarMes">
                   	<input type="hidden" name="funcao" value="lancamentosBuscarMes">
                    <input type="hidden" class="conta_id" name="conta_id" value="<?php echo $conta_id_ini?>">                  
                    <input type="hidden" name="tp_busca" value="mes">
                    <div class="formRight">
                        <select name="mes" style="width:auto" >
                          <option value="" selected="selected">SELECIONE O MÊS</option>
                          <option value="01" >Janeiro</option>
                          <option value="02" >Fevereiro</option>
                          <option value="03" >Março</option>
                          <option value="04" >Abril</option>
                          <option value="05" >Maio</option>
                          <option value="06" >Junho</option>
                          <option value="07" >Julho</option>
                          <option value="08" >Agosto</option>
                          <option value="09" >Setembro</option>
                          <option value="10" >Outubro</option>
                          <option value="11" >Novembro</option>
                          <option value="12" >Dezembro</option>
                        </select>
                        &nbsp;&nbsp; <a href="#" title="" class="button blueB" onClick="lancamentosBuscarMes();"><img src="images/icons/light/magnify.png" alt="" class="icon"><span>Buscar</span></a>
                     </div>  
                  </form>
              </div>
                
            </div>
    
    
    </div> <!-- Fim Buscar período -->
    
 
 	</div> <!-- Fim Fluid -->
 
 <!-- Dynamic table -->
 
  <div class="widget">
      <div class="title"><img src="images/icons/dark/money2.png" alt="" class="titleIcon" /><h6><span class="blue" id="nomeConta"> <?php echo $nome_conta_ini;?> </span> </h6></div>
      <div id="lancamentos">
        <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
        <thead>
        <tr>
          <th>Compensação</th>
          <th>Descrição</th>
          <th>Valor</th>
          <th width="100">Opções</th>
        </tr>
        </thead>
        <tbody>
        <?php

				$query_lancamentos = "
					(select id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
					 from lancamentos
					 where conta_id = ".$conta_id_ini."
					   and compensado = 1)
				
					 union all
				
					(select id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
					 from lancamentos
					 where conta_id_origem = ".$conta_id_ini."
					   and compensado = 1)
						 						
					 union all
				
					(select id, tipo, date_format(dt_compensacao, '%d/%m/%Y') dt_compensacao, descricao, valor, conta_id_origem, conta_id_destino
					 from lancamentos
					 where conta_id_destino = ".$conta_id_ini."
					   and compensado = 1)					 
					
					order by dt_compensacao
				";

        $array_lancamentos = $db->fetch_all_array($query_lancamentos);
        
				foreach($array_lancamentos as $lancamento){
					if($lancamento[tipo]=="R"){
						$classe_excluir = "recebimentosExcluir";
						$valor = '<font color="#009900">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
					}elseif($lancamento[tipo]=="P"){
						$classe_excluir = "pagamentosExcluir";
						$valor = '<font color="#FF0000">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
					}else{
						$classe_excluir = "transferenciasExcluir";
						if($lancamento[conta_id_destino]==$conta_id_ini){
							$valor = '<font color="#009900">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';
						}else{
							$valor = '<font color="#FF0000">R$ '.number_format($lancamento['valor'],2,',','.').'</font>';				
						}
					}
          echo '
            <tr class="gradeA">
              <td>'.$lancamento['dt_compensacao'].'</td>
              <td>'.$lancamento['descricao'].'</td>
              <td>'.$valor.'</td>
              <td class="center">
                <a href="javascript://void(0);" title="Editar" class="smallButton" style="margin: 5px;" onClick="lancamentosExibir('.$lancamento[id].',\''.$lancamento[tipo].'\')"><img src="images/icons/dark/pencil.png" alt=""></a>
                <a href="'.$lancamento[id].'" title="Excluír" class="smallButton '.$classe_excluir.'" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a></td>
              </td>
            </tr>
          ';
        }
        ?>            
        </tbody>
        </table>
      </div>
  </div>
 
 <!-- ====== Fim do Palco ====== -->

  <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php include("dialog_rcbt_incluir.php"); ?>
  
  <?php include("dialog_rcbt_editar.php"); ?>

  <?php include("dialog_pgto_incluir.php"); ?>
  
  <?php include("dialog_pgto_editar.php"); ?>

  <?php include("dialog_trans_incluir.php"); ?>
  
  <?php include("dialog_trans_editar.php"); ?>
 
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 