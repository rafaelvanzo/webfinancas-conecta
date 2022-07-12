<!-- <script> alert(window.innerWidth); </script> --> 
 
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Geral</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php //include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
      
  <!-- Breadcrumbs -->
	<!--
  <div class="wrapper">  
    <div class="bc" style="margin:2px 0 0 0;">
      <ul id="breadcrumbs" class="breadcrumbs">
	      <li class="current"><a href="javascript://" style="cursor: default;">Geral</a></li>
      </ul>
    </div>  
  </div> 
  -->
  <!-- Fim Breadcrumbs -->

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
 
        <!-- =================== Palco =================== -->
 <?php        
      
          $contador_id = $db->fetch_assoc('SELECT contador_id FROM conexao WHERE conectado = 1');    // remover o 'AND id = 79''       
      if($contador_id == true){  
          $db_wf = new Database("mysql.webfinancas.com","webfinancas","W2BSISTEMAS","webfinancas");
          
          $informativo = $db_wf->fetch_all_array('SELECT id, titulo, dt_final FROM clientes_informativo WHERE contador_id ='.$contador_id['contador_id'].' AND situacao = 1 ORDER BY dt_inicio DESC');
         
          $ic = 0;
          foreach($informativo as $info){
              
              //Verifica se a data final é maior do que hoje e inativa        
              if($info['dt_final'] == '0000-00-00' || strtotime($info['dt_final']) >= strtotime(date('Y-m-d'))){
                 
                  $ic++;
                  
                  if($ic > 1){ $hiden = "style='display:none;'";}
                  
                  echo'<div '.$hiden.' class="nNote nInformation info'.$ic.'" onClick="visualizarInfo('.$info['id'].');" >
                    <p><strong>INFORMATIVO: </strong>'.$info['titulo'].' - <a href="javascript:void(0);" onClick="javascript:alert("teste");"><b>Saiba mais.</b></a></p>
                   </div>'; 
                  
              }else{              
                  $registro_inativar['situacao'] = 0; 
                  $db_wf->query_update('clientes_informativo', $registro_inativar, ' id = '.$info['id']);
              }
          }
          
     //$db_wf->close(); 
      }
            
    ?>      

  
         
        <div class="fluid">  
   
      <!--  <div class="example">
          <div id="thing1" class="tour-block">Hey look at me!</div>
          <div id="thing2" class="tour-block last">No look at me!</div>
          <div style="clear: both"></div>
        </div>
      </span></div>
    </section> 
  </div>-->

 <!-- Contas -->

  <?php
  $array_contas = $db->fetch_all_array("
    select c.id, b.nome, b.logo, c.descricao, c.vl_saldo, c.vl_credito
    from contas c
    left join bancos b on c.banco_id = b.id
    order by c.descricao
  ");
  ?>

  <div class="span5">
  
      <div class="widget">

        <div class="title"><img src="images/icons/dark/money.png" alt="" class="titleIcon"><h6>Saldos</h6> <div class="num"><a href="javascript://" class="blueNum tipE" style="cursor: default;" original-title="Total de <?php echo count($array_contas)?> conta(s)"><?php echo count($array_contas)?></a></div> </div>
        <div class="scroll" style="height:136px;">
             <input type="hidden" id="conta_id" value="<?php echo $array_contas[0]['id'];?>" /> <!-- armazena a id da conta que está sendo exibida na página de lançamentos(usado pela função de editar e de excluir) -->
             <div class="newOrder" id="contasSaldo">

               <?php
               $saldo_total = $array_contas[0]['vl_saldo']+$array_contas[0]['vl_credito'];
               $caixa_total = $array_contas[0]['vl_saldo'];
               $credito_total = $array_contas[0]['vl_credito'];
               //$nome_conta_ini = $array_contas[0]['descricao']." - ".$array_contas[0]['nome'];
               $conta_id_ini = $array_contas[0]['id'];

                if(!empty($array_contas[0]['logo'])){ $banco_logo = $array_contas[0]['logo']; }else{ $banco_logo = "bank.png"; }

                echo '
                  <div class="userRow">
                      <img src="images/bancos/'.$banco_logo.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;">
                      <ul class="leftList">
                          <li><a href="javascript://void(0);" style="cursor: default;"><strong>'.$array_contas[0]['descricao'].'</strong></a></li>
                          <li style="font-size: 9px;">'.$array_contas[0]['nome'].'</li>
                      </ul>
                  </div>
                  <div class="orderRow">
                      <ul class="leftList">
                          <li>Saldo:</li>
                          <li>Crédito disponível:</li>
                          <li><b>Total:</b></li>
                      </ul>
                      <ul class="rightList">
                          <li>R$ '.number_format($array_contas[0]['vl_saldo'],2,',','.').'</li>
                          <li>R$ '.number_format($array_contas[0]['vl_credito'],2,',','.').'</li>
                          <li><strong class="grey">R$ '.number_format($array_contas[0]['vl_saldo']+$array_contas[0]['vl_credito'],2,',','.').'</strong></li>
                      </ul>
                  </div>
                  <div class="linha" style="margin-bottom:0;"></div>  <!-- Linha deve estar no ultimo formRow -->
                ';

               unset($array_contas[0]);

               foreach($array_contas as $conta){
                 if(!empty($conta['logo'])){ $banco_logo = $conta['logo']; }else{ $banco_logo = "bank.png"; }
                 
                echo '
                  <div class="userRow">
                      <img src="images/bancos/'.$banco_logo.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;">
                      <ul class="leftList">
                          <li><a href="javascript://void(0);" style="cursor: default;"><strong>'.$conta['descricao'].'</strong></a></li>
                          <li style="font-size: 9px;">'.$conta['nome'].'</li>
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
                  <div class="linha" style="margin-bottom:0;"></div>  <!-- Linha deve estar no ultimo formRow -->
                ';
                $saldo_total += $conta['vl_saldo']+$conta['vl_credito'];
                $caixa_total += $conta['vl_saldo'];
                $credito_total += $conta['vl_credito'];
                
                $total_disponivel = "R$ ".number_format($saldo_total,2,',','.');
               }
               ?>
            </div>
        </div> 

        <!--<div class="cLine"></div>-->

       
             <div class="toggle tipS" style="border-top: 1px solid #CCC; border-bottom: 0; margin-top: 0px;"  original-title="Detalhamento">
                <div class="title closed" id="opened" align="right" style="vertical-align: central; border: 0;">
                  <img src="images/icons/dark/full2.png" alt="" class="titleIcon tipS" />
                      <div style="padding-top: 8px; margin-right: 10px;"> Disponível:<b> <?php echo $total_disponivel; ?> </b> </div>
                 </div>
                <div class="body" style="padding:0;border:0;display:none">
                  <table cellpadding="0" cellspacing="0" width="100%" class="sTable" style=" border-top: 1px solid #CCC;">
                      <tbody>
                          <tr>
                              <td width="40"><b class="blue">Saldo</b></td>
                              <td align="right"><b class="blue"> R$ <?php echo $db->valorFormat($caixa_total);?> </b></td>
                          </tr>
                          <tr>    
                              <td width="40"><b class="blue">Crédito</b></td>
                              <td align="right"><b class="blue"> R$ <?php echo $db->valorFormat($credito_total);?> </b></td>
                          </tr> 
                      </tbody>
                  </table>   
                </div>
            </div> 
           
     </div>

   </div>  <!-- Fim Contas -->   
       
   <!-- Lançamentos Programados--> 
   <?php //include("programacao.php");
		$dt_ini = date('Y').'-'.date('m').'-01';
		//echo $dt_fim = date('Y-m-d', strtotime($dt_ini. ' + 1 month'));
    //"2022-01-31";//mktime(0,0,0,date('m')+1,'00',date('Y'));
		//$dt_fim = date('Y-m-d',$dt_fim);
    $dt_fim = date('Y-m-t',strtotime($dt_ini));
		
		$db->query("
			CREATE TEMPORARY TABLE lancamentos_recorrentes_temp (
				id int(11),
				tipo char(1),
				nm_favorecido varchar(255),
				dt_vencimento date NOT NULL,
				dt_prox_venc date NOT NULL,
				descricao varchar(255),
				valor decimal(10,2) NOT NULL,
				frequencia int(3),
				dia_mes int(1),
				recorrente int(1)
			) ENGINE=MEMORY
		");
		
		$array_lancamentos_rcr = $db->fetch_all_array("select id from lancamentos_recorrentes where (tipo = 'R' || tipo = 'P') and dt_vencimento >= '".$dt_ini."' and dt_vencimento <= '".$dt_fim."' ORDER BY id");
    
		foreach($array_lancamentos_rcr as $lancamento){
		
			$lancamento_rcr = $db->fetch_assoc("
				select lr.tipo, f.nome nm_favorecido, lr.id, dt_vencimento, descricao, valor, frequencia, dia_mes, 1 recorrente, dt_prox_venc 
				from lancamentos_recorrentes lr, favorecidos f 
				where lr.id = ".$lancamento['id']."
					and lr.favorecido_id = f.id
				"
			);
			
			$dt_vencimento = date($lancamento_rcr['dt_vencimento']);

      $count = 0;
		
			while($dt_vencimento <= $dt_fim){
			
				$db->query_insert('lancamentos_recorrentes_temp',$lancamento_rcr);
			
				if($lancamento_rcr[frequencia]>=30){
				
					$frequencia = $lancamento_rcr[frequencia]/30;
					$dia_vencimento = $lancamento_rcr[dia_mes];
					$dt_vencimento_atual = explode('-',$dt_vencimento);
					$mes_prox_venc = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,'1',$dt_vencimento_atual[0]);
					$qtd_dias_mes = date('t',$mes_prox_venc);
		
					if( $qtd_dias_mes < $dia_vencimento ){
						$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$qtd_dias_mes,$dt_vencimento_atual[0]);
						$dt_vencimento = date('Y-m-d',$dt_vencimento);
					}else{
						$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1]+$frequencia,$dia_vencimento,$dt_vencimento_atual[0]);
						$dt_vencimento = date('Y-m-d',$dt_vencimento);
					}
				
				}else{
		
					//$dt_vencimento_atual = explode('-',$dt_vencimento);
					//$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
					//$dt_vencimento = date('Y-m-d',$dt_vencimento);
          $dt_vencimento = date('Y-m-d', strtotime($dt_vencimento. ' + 7 days'));

				}
			
				$lancamento_rcr[dt_vencimento] = $dt_vencimento;
			
			}
		
		}
		
		//fim da busca por lançamentos recorrentes
		
		$query_lancamentos = "
			(select l.tipo, f.nome nm_favorecido, l.id, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, descricao, valor, 0 recorrente, '' dt_prox_venc
			from lancamentos l, favorecidos f
			where (tipo = 'R' || tipo = 'P')
				and compensado = 0
				and dt_vencimento >= '".$dt_ini."'
				and dt_vencimento <= '".$dt_fim."'
				and l.favorecido_id = f.id)
				
			union
		
			(select tipo, nm_favorecido, id, date_format(dt_vencimento, '%d/%m/%Y') dt_vencimento_format, dt_vencimento, descricao, valor, recorrente, dt_prox_venc
			from lancamentos_recorrentes_temp)
			
			order by dt_vencimento
		";
		
		//armazena valores para gerar gráfico previsto x realizado
		$valor_rcbt_rcr = $db->fetch_assoc("select sum(valor) valor from lancamentos_recorrentes_temp where tipo = 'R'");
		$valor_pgto_rcr = $db->fetch_assoc("select sum(valor) valor from lancamentos_recorrentes_temp where tipo = 'P'");
		//fim armazena valores para gerar gráfico previsto x realizado
		
		$array_lancamentos = $db->fetch_all_array($query_lancamentos);
		
		echo '
		<div class="span7">
					 
						<div class="widget" style="height: 216px;">
							<div class="title"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"/><h6>Programação</h6> <div class="num" ><a href="javascript://" class="blueNum tipE"  style="cursor: default;" original-title="Total de '.count($array_lancamentos).' lançamento(s)">'.count($array_lancamentos).'</a></div> </div>
								<div class="scroll" style="height: 176.5px;">
									<div class="updates">
		';
								if(count($array_lancamentos) != 0){ 
									foreach($array_lancamentos as $lancamento){
					
										$dt_vencimento = $lancamento['dt_vencimento_format'];
					
										$data = explode('-',$lancamento['dt_vencimento']);
										$dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
										$hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
										$atraso = $hoje - $dt_limite;
										//$atraso = date('d',$atraso);
										$atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
									 
										if(date('Y-m-d') > date('Y-m-d',$dt_limite)){
											$tempo_atraso = "red";	}else{ $tempo_atraso = ""; }
											
										$m = date('m',strtotime($lancamento['dt_vencimento']));
										if($m == 1){ $mes = 'Jan';}
										elseif($m == 2){ $mes = 'Fev';}
										elseif($m == 3){ $mes = 'Mar';}
										elseif($m == 4){ $mes = 'Abr';}
										elseif($m == 5){ $mes = 'Mai';}
										elseif($m == 6){ $mes = 'Jun';}
										elseif($m == 7){ $mes = 'Jul';}
										elseif($m == 8){ $mes = 'Ago';}
										elseif($m == 9){ $mes = 'Set';}
										elseif($m == 10){ $mes = 'Out';}
										elseif($m == 11){ $mes = 'Nov';}
										else{ $mes = 'Dez';}
		
										if($lancamento['tipo']=="R"){
											$valor_classe = "blue";
											$sinal_classe = "lReceita";
										}else{
											$valor_classe = "red";
											$sinal_classe = "lDespesa";
										}
										
																		
									//	if( $atraso > "0" ){ $classe_atrasado = "red"; }else{ $classe_atrasado = " ";}
					
										echo '
											<!-- Lançamento -->
											<div class="newUpdate">
											
											<div original-title="Vencimento" class="tipS uDate" align="center" ><span class="uDay ',$tempo_atraso,'">',date('d',strtotime($lancamento['dt_vencimento'])),'</span>',$mes,'/',date('y',strtotime($lancamento['dt_vencimento'])),'</div>
											
												<span class="',$sinal_classe,'">
													<a href="#" original-title="Descrição" class="tipS" style="cursor: default;"><strong>',$lancamento['descricao'],'</strong></a>
														<span original-title="Favorecido" class="tipN">',$lancamento['nm_favorecido'],'</span>
												</span>
											 <!-- <a href="#" title="" class="smallButton" style="float: right; margin: 5px 0 5px 15px;"><img src="images/icons/color/tick.png" title="Compensar lançamento"></a> -->
												<div style="float:right; padding:10px 5px; font-size:16px; font-weight:bold;" class="',$valor_classe,'">R$ ',$db->valorFormat($lancamento['valor']),'</div>
												 
											</div>
											<!-- Fim Lançamento -->
										';
									}	
								} else{ echo '<div align="center" style="margin-top: 10%;"> <b>Nenhuma programação próxima a vencer.</b> </div>'; }
		echo '
									</div>
								</div>
					 </div> 
										
		</div>
		';
	?> <!-- Fim Lançamentos Programados--> 

	</div> <!-- Fim Fluid -->

  <div class="fluid">
    
    <!-- Gráfico Pizza Receita x Despesa Mensal (Prevista) -->  
    <div class="span4">
    
            <div class="widget" style="height: 330px;">
                <div class="title tipS" original-title="Entradas x Saídas ( Mensal )" ><img src="images/icons/dark/chart8.png" alt="" class="titleIcon" /><h6>Entradas x Saídas</h6></div>
                <div class="body"><div class="pie" id="donut"></div></div>
            </div>
            
       </div>
       
       <div class="span8">
         <div class="widget" style="min-height: 330px;" align="center">
                  <div class="title tipS" original-title="Movimentação Financeira ( Mensal )" ><img src="images/icons/dark/stats.png" alt="" class="titleIcon"><h6>Movimentação Financeira</h6></div>
                 		<div class="body">
                    	<div class="bars placeholder1" style="width:28%;display:inline-block; margin: 0 2% 0 2%;" id="barraEntrada"></div>
                      <div class="bars placeholder2" style="width:28%;display:inline-block; margin: 0 2% 0 2%;" id="barraSaida"></div>
                      <div class="bars placeholder3" style="width:28%;display:inline-block; margin: 0 2% 0 2%;" id="barraTotal"></div>
                    </div>

                  <!--    <table cellpadding="0" cellspacing="0" width="100%" class="sTable" style="border-top: 1px solid #CCC; ">
                          <thead>
                              <tr>
                                  <td width="60">Descrição</td>
                                  <td>Contas quitadas</td>
                                  <td>Contas à quitar</td>
                              </tr>
                          </thead>
                          <tbody>
                      		 		<tr>
                              		<td>Entradas</td>                                  
                                  <td>R$ <?php// echo $db->valorFormat($receitas_realizadas[valor]);?></td>
                                  <td>R$ <?php// echo $db->valorFormat($receitas_previstas[valor]);?></td>
                              </tr>
                              <tr>    
                                  <td>Saídas</td>                                  
                                  <td>R$ <?php// echo $db->valorFormat($despesas_realizadas[valor]);?></td>
                                  <td>R$ <?php// echo $db->valorFormat($despesas_previstas[valor]);?></td>
                              </tr> 
                              <tr>    
                                  <td><b>Saldo</b></td>                                  
                                  <td>R$ <?php// echo $db->valorFormat(($receitas_realizadas[valor]-$despesas_realizadas[valor]));?></td>
                                  <td>R$ <?php// echo $db->valorFormat(($receitas_previstas[valor]-$despesas_previstas[valor]));?></td>
                              </tr>
                          </tbody>
                      </table>     -->

              </div>  
       </div>
  
	</div> <!-- Fim Fluid -->
  
  	<!-- Gráfico Receita x Despesas por um exercício (anual) -->
        <div class="widget chartWrapper">
            <div class="title tipS" original-title="Entradas x Saídas ( Anual )" ><img src="images/icons/dark/graph.png" alt="" class="titleIcon" /><h6>Entradas x Saídas</h6></div>
            <div class="body"><div class="chart"></div></div>
        </div>    
 
 <!-- ====== Fim do Palco ====== -->
 
 <!-- ====== *** UI Dialogs *** ====== -->
   <?php include("dialog_informativo.php"); ?>
 <!-- ====== *** Fim UI Dialogs *** ====== -->     
	</div> 
</div>