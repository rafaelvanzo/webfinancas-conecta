<?php
$dt_ini = date('Y').'-'.date('m').'-01';
$dt_fim = mktime(0,0,0,date('m')+1,'00',date('Y'));
$dt_fim = date('Y-m-d',$dt_fim);

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

$array_lancamentos_rcr = $db->fetch_all_array("select id from lancamentos_recorrentes where (tipo = 'R' || tipo = 'P') and dt_vencimento >= '".$dt_ini."' and dt_vencimento <= '".$dt_fim."'");
foreach($array_lancamentos_rcr as $lancamento){

	$lancamento_rcr = $db->fetch_assoc("
		select lr.tipo, f.nome nm_favorecido, lr.id, dt_vencimento, descricao, valor, frequencia, dia_mes, 1 recorrente, dt_prox_venc 
		from lancamentos_recorrentes lr, favorecidos f 
		where lr.id = ".$lancamento[id]."
			and lr.favorecido_id = f.id
		"
	);
	
	$dt_vencimento = date($lancamento_rcr[dt_vencimento]);

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

			$dt_vencimento_atual = explode('-',$dt_vencimento);
			$dt_vencimento = mktime(0,0,0,$dt_vencimento_atual[1],$dt_vencimento_atual[2]+7,$dt_vencimento_atual[0]);
			$dt_vencimento = date('Y-m-d',$dt_vencimento);

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
       
       	<div class="widget" style="height: 213px;">
          <div class="title"><img src="images/icons/dark/money2.png" alt="" class="titleIcon"/><h6>Programação</h6> <div class="num" ><a href="javascript://" class="blueNum tipE"  style="cursor: default;" original-title="Total de '.count($array_lancamentos).' lançamento(s)">'.count($array_lancamentos).'</a></div> </div>
            <div class="scroll" style="height: 173px;">
              <div class="updates">
';
						if(count($array_lancamentos) != 0){ 
							foreach($array_lancamentos as $lancamento){
			
								$dt_vencimento = $lancamento[dt_vencimento_format];
			
								$data = explode('-',$lancamento[dt_vencimento]);
								$dt_limite = mktime(0,0,0,$data[1],$data[2],$data[0]);
								$hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
								$atraso = $hoje - $dt_limite;
								//$atraso = date('d',$atraso);
								$atraso = (int)floor( $atraso / (60 * 60 * 24)); //  Calcula a diferença de dias
							 
								if(date('Y-m-d') > date('Y-m-d',$dt_limite)){
									$tempo_atraso = "</b> <font class='subTexto red'>  ( Atrasado ".$atraso." dia(s) )</font>";	}else{ $tempo_atraso = ""; }
									
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
									$valor_classe = "green";
									$sinal_classe = "lReceita";
								}else{
									$valor_classe = "red";
									$sinal_classe = "lDespesa";
								}
								
																
							//	if( $atraso > "0" ){ $classe_atrasado = "red"; }else{ $classe_atrasado = " ";}
			
								echo '
									<!-- Lançamento -->
									<div class="newUpdate">
									
									<div class="uDate" align="center"><span class="uDay">'.date('d',strtotime($lancamento['dt_vencimento'])).'</span>'.$mes.'/'.date('y',strtotime($lancamento['dt_vencimento'])).'</div>
									
										<span class="'.$sinal_classe.'">
											<a href="#"><strong>'.$lancamento['descricao'].'</strong></a>
												<span>'.$lancamento['nm_favorecido'].$tempo_atraso.'</span>
										</span>
									 <!-- <a href="#" title="" class="smallButton" style="float: right; margin: 5px 0 5px 15px;"><img src="images/icons/color/tick.png" title="Compensar lançamento"></a> -->
										<div style="float:right; padding:10px 5px; font-size:16px; font-weight:bold;" class="'.$valor_classe.'">R$ '.$db->valorFormat($lancamento['valor']).'</div>
										 
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
?>