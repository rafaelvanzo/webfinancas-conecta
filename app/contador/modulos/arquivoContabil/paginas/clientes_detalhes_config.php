<?php
$clienteId = $_GET['id_registro'];

if(!isset($_SESSION['contador_cliente']))
    $_SESSION['contador_cliente'] = array('id'=>$clienteId,'nome'=>'');

$v_clientes = $db->fetch_assoc('select distinct id from conexao where cliente_id ='.$clienteId.' order by id DESC');

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
                <h2>Configurações - <?php echo $clientes_dados['nome']; ?></h2>
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

		<br />

    <!-- Botões -->
    <div class="wrapper">

      <div class="fluid">

        <div class="span6">
       	    <a href="clientesDetalhes/<?php echo $clienteId?>" title="" class="button redB" ><img src="<?php echo $raiz;?>images/icons/light/arrowLeft.png" alt="" class="icon"><span>Arquivo Contábil</span></a>
            <!--<a href="clientesDetalhes/<?php //echo $clienteId?>" title="" class="button basic" onClick="" style="padding:7px">Arquivo Contábil</a>-->
            <br /><br />
            <div class="selecionar" style="width:375px;height:35px">
                <input type="radio" id="tp-config-01" name="tp_config" value="1"checked /> <label for="tp-config-01"> Contas Financeiras </label> &nbsp | &nbsp <input type="radio" id="tp-config-02" name="tp_config" value="2"/> <label for="tp-config-02"> Plano de Contas </label> &nbsp | &nbsp <input type="radio" id="tp-config-03" name="tp_config" value="3"/> <label for="tp-config-03"> Favorecidos </label>
            </div>
        </div>
        
        <div class="span6" align="right">
        </div>
      
      </div>
      
    </div>

    <!-- Botões -->
    <div class="wrapper">
        <br />
	    <a href="#" title="" class="button greenB" style="display:none;" id="btn-nova-categoria"><img src="<?php echo $raiz;?>images/icons/light/add.png" alt="" class="icon"/><span>Nova Categoria</span></a>
    </div>

    <!-- main content wrapper -->
    <div class="wrapper">

        <!-- =================== Palco =================== -->
           
 <!-- Organiza o layout -->
 <div class="fluid">

     <div class="span12">
         <div class="widget">
                   <!-- <div class="title" ><img src="<?php echo $raiz;?>images/icons/dark/cog.png" alt="" class="titleIcon" /><h6>Configração</h6> </div>-->
                       
                  
                  <form id="salvarConfigContas">
                  <input type="hidden" name="funcao" value="salvarPlConfigContas" />
                  <input type="hidden" name="cliente_id" value="<?php echo $clienteId;?>" />
                  
             	<table cellpadding="0" cellspacing="0" width="100%" class="sTable" id="tbl-cf">
                        <thead>
                            <tr>
                                <td>Contas Financeiras</td>
                                <td width="80">Conta balanço</td>
                            </tr>
                        </thead>
                        <tbody>
              
             
							<?php	
							$contas = $db_cliente_conexao->fetch_all_array('select id, banco_id, descricao from contas');
							$cfCont = 0;
							if(count($contas)>0){
                                foreach($contas as $conta){

                                    $cfCont++;

                                    $bancos = $db_cliente_conexao->fetch_assoc('select nome, logo from bancos where id ='.$conta['banco_id']);	
                                    
                                    if(empty($bancos['logo'])){$logo = "bank.png"; }else{ $logo = $bancos['logo'];}
                                    
                                    //resgata os valores dentro do banco do contador
                                    $contador_cf_cod = $db->fetch_assoc('select contador_cf_cod from clientes_cf where cliente_id ='.$clienteId.' and cliente_cf_id ='.$conta['id']);															
                                    
                                    echo '	
								    <tr class="gradeA">
								    <td class="updates newUpdate">
												
										    <div class="uDate tbWF" align="center" style="padding-right:8px; padding-bottom: 5px; margin-right:-8px; "> 
										    <img src="'.$raiz.'images/bancos/'.$logo.'" alt="" class="floatL" style="-webkit-border-radius : 2px; -moz-border-radius: 2px;"></div>
											    <span class="lDespesa tbWF">
												    <a href="javascript://void(0);" style="cursor: default;" original-title="Descrição" class="tipS"><strong>'.$conta['descricao'].'</strong></a>
													    <span original-title="Instituição Financeira" class="tipN">'.$bancos['nome'].'</span>
											    </span>																																																								
				
							        </td>
								    <td class="updates newUpdate" align="center">
									    <input type="hidden" name="cliente_cf_id',$cfCont,'" value="',$conta['id'],'">
									    <input type="text" name="contador_cf_cod',$cfCont,'" value="',$contador_cf_cod['contador_cf_cod'],'" maxlength="30" style="width:100px; text-align:center;">
								    </td>
								    </tr>';
                                    
                                    //}
                                }
                            }
               				?>	
             	            <tr>
                                <td colspan="2" align="right">
                  	                <a href="javascript://" class="button greenB" onClick="salvar_config_contas();"><img src="<?php echo $raiz;?>images/icons/light/check.png" alt="" class="icon"><span>Salvar</span></a>
            	                </td>
                            </tr>                            
                      </tbody>
                    </table>  


                    <input type="hidden" name="cfTotal" value="<?php echo $cfCont;?>" />


            </form>






                    <!-- start: Plano de contas -->
                    <div id="plano-contas">


            <form id="salvarConfigPlano">
                  <input type="hidden" name="funcao" value="salvarPlConfigPlano" />
                  <input type="hidden" name="cliente_id" value="<?php echo $clienteId;?>" />


                    <?php
                    //busca plano de contas de forma ordenada
                    //$array_plano_contas = $db_cliente_conexao->fetch_all_array('select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,",",1) as unsigned) as ordem from plano_contas where cod_conta > 0 order by ordem, cod_conta');
                    //fim busca plano de contas de forma ordenada

                    //start: busca plano de contas ordenado
                    $maiorNivel = $db_cliente_conexao->fetch_assoc('select max(nivel) nivel from plano_contas');
                    $maiorNivel = $maiorNivel['nivel'];

                    $ordem = '';
                    $orderBy = '';

                    if($maiorNivel>1){
                        
                        $arrayOrderBy = array();
                        $arrayOrdem = array();

                        for($i=2;$i<$maiorNivel;$i++){
                            array_push($arrayOrderBy,'ordem'.$i);
                            array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(substring_index(cod_conta,".",'.$i.'),".",-1) as decimal),0) as ordem'.$i);
                        }

                        array_push($arrayOrdem,'if(nivel>='.$i.',cast(substring_index(cod_conta,".",-1) as decimal),0) as ordem'.$i);
                        array_push($arrayOrderBy,'ordem'.$i);

                        $ordem = ','.join(',',$arrayOrdem);
                        $orderBy = ','.join(',',$arrayOrderBy);
                    }

                    $array_plano_contas = $db_cliente_conexao->fetch_all_array('
                    select id, cod_conta, nome, tp_conta, nivel, cast(substring_index(cod_conta,".",1) as decimal) as ordem1'.$ordem.'
                    from plano_contas 
                    where cod_conta > 0 
                    order by ordem1'.$orderBy);
                    //end: busca plano de contas ordenado

                    $cont = 0;
                    if(count($array_plano_contas)>0){

                    ?>

                        <table cellpadding="0" cellspacing="0" width="100%" class="sTable" id="tbl-plc" style="display:none">

                            <thead>
                                <tr style="font-size:14px;">
                                    <td width="100%" align="center">
                                        Plano de Contas
          	                        </td>
                                    <td width="auto" align="center"> Débito / Crédito </td>
                                    <td width="auto" align="center"> Opções </td>
                                </tr>
                            </thead>
                            <tbody>

                            <?php

                            foreach($array_plano_contas as $planoContas){
        
                                $cont++;					

                                $contador_pl_cod = $db->fetch_assoc('select contador_pl_cod from clientes_pl_config where cliente_id ='.$clienteId.' and cliente_pl_id ='.$planoContas['id']);
        
                                $espc = $planoContas['nivel'] * 10;  $espc = $espc.'px';
                                if($planoContas['nivel'] == 1){ $strong = 'bold;'; $fontN = '16px;';  }else{ $strong = 'normal;'; $fontN = '12px;'; }

                                //$valor = $array_valores[$planoContas['id']];
                                if($planoContas['tp_conta'] == '1'){ 
                                    $tpConta = '<spam class="tipN" original-title="Analítico">(A)</span>';
                                    $debito_credito = $contador_pl_cod['contador_pl_cod'];
                                    $disabled = '';
                                    $hidden = '';
                                }else{ 
                                    $tpConta = '<span class="tipN" original-title="Sintético">(S)</span>'; 
                                    $debito_credito = '';
                                    $disabled = 'disabled';
                                    $hidden = 'hidden';
                                }
        
                                echo '
			                                <tr class="gradeA">
				                                <td class="hidden">',$cont,'</td>
				                                <td class="updates newUpdate" >
												
						                                <div class="uDate tbWF tipS" original-title="Nível" align="center" style="width:auto; padding-left:',$espc,'"> <span class="uDay ',$atrasado,'" style="font-size:',$fontN,$cor,'">',$planoContas['cod_conta'],' - </span></div>
							                                <span class="lDespesa tbWF" style="margin-top:2px; font-size:14px;">
								                                <a href="javascript://void(0);" style="cursor: default; font-size:',$fontN,'font-weight:',$strong,$cor,'" original-title="Descrição" class="tipS " >',$planoContas['nome'],'</a>													
							                                </span>											
																									
				                                </td>
				                                <td class="updates newUpdate" >
					                                <input type="hidden" name="cliente_pl_id',$cont,'" value="',$planoContas['id'],'"> 
					                                <input type="text" name="contador_pl_cod',$cont,'" value="',$debito_credito,'" style="width:100px; text-align:center;" maxlength="30" class="form '.$hidden.'" '.$disabled.'>
				                                </td>

                                                <td class="updates newUpdate" >
                                                    <div class="tbWFoption">
        						                        <a href="'.$planoContas['id'].'" original-title="Excluír" class="smallButton btTBwf redB tipS planoContasExcluir" data-cliente-id="'.$clienteId.'"><img src="../../sistema/images/icons/light/close.png" width="10"></a>
				                                        <a href="javascript://void(0);" original-title="Editar" class="smallButton btTBwf greyishB tipS" onClick="planoContasExibir('.$planoContas['id'].','.$planoContas['tp_conta'].','.$clienteId.');"><img src="../../sistema/images/icons/light/pencil.png" width="10"></a>
                                                    </div>
							                    </td>
			                                </tr>
                                        ';

                            }
                                ?>

                                <tr>
                                    <td colspan="3" align="right">
                                        <a href="javascript://" class="button greenB" onClick="salvar_config_plano();"><img src="<?php echo $raiz;?>images/icons/light/check.png" alt="" class="icon"><span>Salvar</span></a>
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                    <?php
                    }else{
                    ?>
                        <table style="display:none;width:100%;" class="sTable" id="tbl-modelo-plc">
                            <thead>
                                <tr style="font-size:14px;">
                                    <td style="text-align-last:center;width:15px;"></td>
                                    <td style="text-align-last:center;"> Modelo </td>
                                    <td style="text-align-last:center;"> Descrição </td>
                                </tr>
                            </thead>
                            <tbody>
                                <!--
                                <tr>
                                    <td><input type="radio" name="modeloPlc" value="padrao" id="modelo-01"/></td>
                                    <td><label for="modelo-01">Padrão</label></td>
                                    <td></td>
                                </tr>
                                -->
                                <tr>
                                    <td><input type="radio" name="modeloPlc" value="engenharia" id="modelo-02"/></td>
                                    <td><label for="modelo-02">Engenharia</label></td>
                                    <td>Plano de contas para a atividade de engenharia.</td>
                                </tr>
                                
                                <tr>
                                    <td><input type="radio" name="modeloPlc" value="odontologico" id="modelo-03"/></td>
                                    <td><label for="modelo-03">Odontológico</label></td>
                                    <td>Plano de contas para a atividade de odontologia.</td>
                                </tr>

                                <?php if($_SESSION['cliente_id'] == 1061) { ?>
                                    <tr>
                                        <td><input type="radio" name="modeloPlc" value="ebitdah" id="modelo-04"/></td>
                                        <td><label for="modelo-04">Modelo Ebitdah</label></td>
                                        <td>Plano de contas criado pela empresa EBITDAH.</td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                        <td><input type="radio" name="modeloPlc" value="gerencial" id="modelo-05"/></td>
                                        <td><label for="modelo-05">Modelo Gerencial</label></td>
                                        <td>Plano de contas gerencial.</td>
                                    </tr>
                                
                                <tr>
                                    <td align="right" colspan="3">
                                        <a href="javascript://" class="button greenB" onClick="CarregarPlanoContas(<?php echo $clienteId;?>);"><img src="<?php echo $raiz;?>images/icons/light/check.png" alt="" class="icon"><span>Carregar</span></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php
                    }
                    ?>                    
                    </div>


                    <input type="hidden" name="plTotal" id="total-categorias" value="<?php echo $cont; ?>" />
                   
            </form>
 <!-- end: Plano de contas-->


            

            <form id="salvarConfigFavorecido">
                  <input type="hidden" name="funcao" value="salvarPlConfigFavorecido" />
                  <input type="hidden" name="cliente_id" value="<?php echo $clienteId;?>" />


              <table cellpadding="0" cellspacing="0" width="100%" class="sTable" id="tbl-fav" style="display:none">

                <thead>
                    <tr>
                        <td>Favorecidos</td>
                        <td width="80">Cliente</td>
                        <td width="80">Fornecedor</td>

                    </tr>
                </thead>
                <tbody>
              
				    <?php
                    $favorecidos = $db_cliente_conexao->fetch_all_array('select id, nome, inscricao, cpf_cnpj from favorecidos ORDER BY id desc');
                    ?>
                        <input type="hidden" name="favTotal" value="<?php echo count($favorecidos) ?>" />
                    <?php
				    $favCont = 0;
                    if(count($favorecidos)>0){
                        foreach($favorecidos as $favorecido){
                                    
                            $favCont += 1;
                                    
                            //resgata os valores dentro do banco do contador
                            $contador_fav_cod = $db->fetch_assoc('select contador_cliente_cod, contador_fornecedor_cod from clientes_favorecidos where cliente_id ='.$clienteId.' and cliente_favorecido_id ='.$favorecido['id']);
                                    
                            echo '
						    <tr class="gradeA">
							    <td class="updates newUpdate">

									    <span class="lDespesa tbWF">
										    <a href="javascript://void(0);" style="cursor: default;" original-title="Nome" class="tipS"><strong>'.$favorecido['nome'].'</strong></a>
										    <span original-title="Inscrição" class="tipN">'.$favorecido['cpf_cnpj'].'</span>
									    </span>																																																								
				
							    </td>
							    <td class="updates newUpdate" align="center">
								    <input type="hidden" name="cliente_favorecido_id',$favCont,'" value="',$favorecido['id'],'">
								    <input type="text" name="contador_cliente_cod',$favCont,'" value="',$contador_fav_cod['contador_cliente_cod'],'" maxlength="30" style="width:100px; text-align:center;">
							    </td>
                                <td class="updates newUpdate" align="center">
								    <input type="text" name="contador_fornecedor_cod',$favCont,'" value="',$contador_fav_cod['contador_fornecedor_cod'],'" maxlength="30" style="width:100px; text-align:center;">
							    </td>
						    </tr>';

                        }
                    }
               	    ?>	

                    
             	    <tr>
                        <td colspan="3" align="right">
                   	        <a href="javascript://" class="button greenB" onClick="salvar_config_favorecido();"><img src="<?php echo $raiz;?>images/icons/light/check.png" alt="" class="icon"><span>Salvar</span></a>
            	        </td>
                    </tr>
                </tbody>
            </table>
           

            
            </form>
             

            </div>           
            
       </div>
           
 </div> <!-- Fim Fluid Tudo --> 
    
    
   <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php //require($raiz."modulos/planoContas/paginas/plano_dialogs.php");?>

<div id="modal-categoria" style="height:auto; padding:0;" title="Nova Categoria" class="modal">
								
    <form id="form_planoContas" class="dialog">
        <input type="hidden" name="funcao" value="" id="funcao-categoria">
	    <input type="hidden" name="cod_conta" value="" />
  	    <input type="hidden" name="nivel" value="" />
	    <input type="hidden" name="posicao" value="" />
        <input type="hidden" name="plano_contas_id" value="">
        <input type="hidden" name="conta_pai_id_ini" id="conta_pai_id_ini" value="">
        <input type="hidden" name="cod_ref_ini" id="cod_ref_ini" value="">
        <input type="hidden" name="clienteId" value="<?php echo $clienteId?>" />

        <div class="fluid">
                    
                <div class="formRow">                  
                <span class="span6 input-autocomplete-container">
                    <label>Categoria Pai:</label>
                    <input type="text" name="buscar_conta_pai_id" id="nm_plc_pai" class="plano_contas_buscar_plc ui-autocomplete-input input-buscar" placeholder="Preencha para localizar...">     
                    <input type="hidden" name="conta_pai_id" value="" id="buscar_conta_pai_id">
                </span>
                <span class="span6">
                    <label>Nome da Categoria:</label>
                    <input type="text" name="nome" value="" class="required"/>
                </span>
                <span class="span2" style="display:none">
                    <label>Código de Referência:</label>
                    <input type="text" name="cod_ref" value=""  onkeydown="Mascara(this,Integer);" onkeypress="Mascara(this,Integer)" onkeyup="Mascara(this,Integer)" />
                </span>
                </div>

                <div class="formRow">
                    <span class="span6">
                    <label>Classificação Fluxo de Caixa:</label>
                    <select name="clfc_fc">
                        <option value=""></option>
                        <option value="1">Entradas Operacionais</option>
                        <option value="2">Saídas Operacionais</option>
                        <option value="3">Investimentos</option>
                        <option value="4">Resgate de Investimentos</option>
                        <option value="5">Receitas Financeiras</option>
                        <option value="6">Financiamentos</option>
                        <option value="7">Pagamentos dos Financiamentos</option>
                        <option value="8">Despesas Financeiras</option>
                        <option value="9">Aporte dos Sócios</option>
                        <option value="10">Pagamento aos sócios</option>
                        <option value="11">Entrada de Tesouraria</option>
                        <option value="12">Saída de Tesouraria</option>
                    </select>
                    </span>
                    <span class="span6">
                    <label>Classificação DRE:</label>
                    <select name="clfc_dre">
                        <option value=""></option>
                        <option value="1">Receitas Operacionais</option>
                        <option value="2">Receitas Financeiras</option>
                        <!--<option value="3">Despesas Operacionais</option>-->
                        <option value="4">Despesas Financeiras</option>
                        <option value="5">Despesas Variáveis</option>
                        <option value="6">Despesas Fixas</option>
                        <option value="7">Custos da Produção - CP</option>
                        <option value="8">Custos da Mercadoria Vendida - CMV</option>
                        <option value="9">Custos do Serviço Prestado - CSP</option>
                        <option value="10">Impostos S/ Vendas</option>
                        <option value="11">Impostos S/ Lucro</option>
                    </select>
                    </span>
                                                                </div>

                <div class="formRow" style="display:none">
                    <span class="span6">
                        <label>Tipo de Categoria:</label>
                        <select name="tp_conta" class="required">
                        <option value="1" selected>(A) Analítico</option>
                        <option value="2">(S) Sintético</option>
                        </select>
                    </span>                    
                </div>

                <div class="formRow">
                    <span class="span6">
                        <label>Tipo de conta:</label>
                        <select name="tpCategoria">
                            <option value="0">Selecione</option>
                            <option value="1">Receita</option>
                            <option value="2">Despesa fixa</option>                            
                            <option value="3">Despesa variável</option>
                        </select>
                    </span>
                </div>
                  
                <div class="formRow"> 
                    <span class="span12">
                        <label>Descrição:</label>
                        <textarea name="descricao" cols="auto" rows="2"></textarea>
                    </span>
                </div>
                
                <?php
                //if($_SESSION['carne_leao']){
                ?>
                <div class="formRow" style="margin-bottom:10px" id="div-ckb-dedutivel">
                    <span class="span2">
                        <label for="ckb-dedutivel01">Dedutível:</label>
                        <input type="checkbox" name="dedutivel" value="1" class="ckb-bootstrap" id="ckb-dedutivel01" style="margin-top:6px;"/>
                    </span>
                </div>
                <?php    
                //}
                ?>
                
            <div class="linha"></div>  <!-- Linha deve estar no ultimo formRow -->                            
                 
        </div>  <!-- fluid -->                 
    </form>                  
</div><!-- Fim dialog --> 

  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
  
	</div> 
</div>