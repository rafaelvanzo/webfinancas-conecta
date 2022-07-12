<?php
$clienteId = $_GET['id_registro']; 

// Verifica se o cliente é desse contador
$v_clientes = $db->fetch_assoc('select distinct id from conexao where cliente_id ='.$clienteId.' order by id DESC');
if($v_clientes == false){ echo "<script> location.href='clientes';</script>"; }
	
$clientes_dados = $db_w2b->fetch_assoc('select * from clientes where id ='.$clienteId);

$conectado = $db->fetch_assoc('select distinct conectado from conexao where cliente_id ='.$clienteId.' order by id DESC');
if($conectado['conectado'] == 1){ $conexao = "Conectado"; $cor = "green"; }else{ $conexao = "Desconectado"; $cor = "red"; };

$db_wf = new Database('mysql.webfinancas.com','webfinancas','W2BSISTEMAS','webfinancas');

$clientes_db = $db_wf->fetch_assoc('select db, db_senha from clientes_db where cliente_id ='.$clienteId.' and contador = 0');
$db_cli = new Database('mysql.webfinancas.com',$clientes_db['db'],$clientes_db['db_senha'],$clientes_db['db']);

?>

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Arquivo Contábil - <?php echo $clientes_dados['nome']; ?></h2>
                <!--<span style="padding-top:15px;font-size:14px;">teste</span> -->
            </div>

	     </div>
    </div>
    <!-- Fim título -->

	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>
     

    <!-- Botões -->
    <br />

    <div class="wrapper">
    	
      <div class="fluid">

        <div class="span6">
          <a href="arquivoContabil" title="" class="button redB" ><img src="<?php echo $raiz;?>images/icons/light/arrowLeft.png" alt="" class="icon"><span>Clientes</span></a>
        </div>
        
        <div class="span6" align="right">
        </div>
      
      </div>
      
    </div>
    
    <div class="wrapper">
    
      <div class="fluid">
        
        <div class="span12">
          
          <div class="widget">
    
          	<form id="form1">

            	<input type="hidden" name="cliente_id" value="<?php echo $clienteId; ?>" id="cliente_id"/>

              <div class="formRow">

                <!-- Contas financeiras -->
                <span class="span3">
  
                  <label>Conta Financeira:</label>
  
                  <div class="selecionar" style="min-height:140px">
  
                    <input type="checkbox" id="contasChecarTodos"/> <label for="contasChecarTodos" style="float:none;display:inline-block;padding-top:1px;">Selecionar todos</label> <br />
  
                    <span id="contas-financeiras">
  
                      <?php
                      /*  
                      select c.id, ifnull(concat(nome,' - ',descricao),descricao) nome
                        from contas c
						            join bancos b on c.banco_id = b.id
                        order by nome
                        */
                      $array_contas_financeiras = $db_cli->fetch_all_array("
                        select id, banco_id, descricao
                        from contas 
                      ");
                      $cfCont = 0;
					  foreach($array_contas_financeiras as $conta_financeira){
						$cfCont++;
                        echo '<input type="checkbox" name="banco'.$cfCont.'" value="'.$conta_financeira['id'].'" class="contaCheckbox" id="checkbox-cf-'.$conta_financeira['id'].'"/> <label for="checkbox-cf-'.$conta_financeira['id'].'" style="float:none;display:inline-block;padding-top:1px;">'.$conta_financeira['descricao'].' </label><br />';
                      }
                      ?>

					  <input type="hidden" name="bancoTotal" value="<?php echo $cfCont; ?>" />
                      
                    </span>
  
                  </div>
                  
                  <br>
                  
                </span>

                <!-- Tratamento contábil para receitas -->
                <span class="span3">
  
                  <label>Tratamento Contábil - Receitas:</label>
  
                  <div class="selecionar" style="min-height:140px">
  
                    <!--<input type="checkbox" id="tc-rcbt-check-all"/> <label for="tratamento-check-all" style="float:none;display:inline-block;padding-top:1px;">Selecionar todos</label> <br />-->
  
                    <span id="tratamento-contabil-rcbt">
  
                        <input type="radio" id="rcbt-prov-baixa" value="1" name="rtc" class="rtc"/> <label for="rcbt-prov-baixa" style="float:none;display:inline-block;padding-top:1px;">Provisão e Baixa</label> <br />
                        <input type="radio" id="rcbt-baixa-cliente" value="2" name="rtc" class="rtc"/> <label for="rcbt-baixa-cliente" style="float:none;display:inline-block;padding-top:1px;">Baixa contra cliente</label> <br />
                        <input type="radio" id="rcbt-baixa-receita" value="3" name="rtc" class="rtc"/> <label for="rcbt-baixa-receita" style="float:none;display:inline-block;padding-top:1px;">Baixa contra receita</label> <br />
                   
                    </span>
  
                  </div>
                   
                  <br>
                  
                </span>

                <!-- Tratamento contábil para despesas -->
                <span class="span3">
  
                  <label>Tratamento Contábil - Despesas:</label>
  
                  <div class="selecionar" style="min-height:140px">
  
                    <!--<input type="checkbox" id="tc-pgto-check-all"/> <label for="tratamento-check-all" style="float:none;display:inline-block;padding-top:1px;">Selecionar todos</label> <br />-->
  
                    <span id="tratamento-contabil-pgto">
  
                        <input type="radio" id="pgto-prov-baixa" value="1" name="ptc" class="ptc"/> <label for="pgto-prov-baixa" style="float:none;display:inline-block;padding-top:1px;">Provisão e Baixa</label> <br />
                        <input type="radio" id="pgto-baixa-favorecido" value="2" name="ptc" class="ptc"/> <label for="pgto-baixa-favorecido" style="float:none;display:inline-block;padding-top:1px;">Baixa contra fornecedor</label> <br />
                        <input type="radio" id="pgto-baixa-despesa" value="3" name="ptc" class="ptc"/> <label for="pgto-baixa-despesa" style="float:none;display:inline-block;padding-top:1px;">Baixa contra despesa</label> <br />
                   
                    </span>
  
                  </div>
                  
                  <br>
                  
                </span>
                <!-- Período -->
                <!--
                <span class="span4">
  
                  <label style="width: 100%;">Período:</label>
                  <input name="mes_ini" id="mes-ini" type="text" class="monthpickerReport" placeholder="Mês inicial" style="text-align:center;width:70px;" readonly/>
                  <input name="mes_fim" id="mes-fim" type="text" class="monthpickerReport" placeholder="Mês final" style="text-align:center;width:70px;" readonly/>
                  
                  <br><br>
                  
                  <label style="width: 100%;">Tratamento contábil:</label>
                  <select name="tratamentoContabil" id="tratamento-contabil" style="width:145px;">
                    <option value="">
                     Selecione..
                    </option>                                      	
                    <option value="1">
                     Baixa
                    </option>
                    <option value="2">
                     Baixa e Financeiro
                    </option>
                  </select>

                  <input type="checkbox" id="rcbt-baixa"/> <label for="rcbt-baixa" style="float:none;display:inline-block;padding-top:1px;">Recebimento - Baixa</label> <br />
                  <input type="checkbox" id="rcbt-prov-baixa"/> <label for="rcbt-prov-baixa" style="float:none;display:inline-block;padding-top:1px;">Recebimento - Provisão e Baixa</label> <br />

                </span>
                -->
                
                <span class="span3" style="padding-top:">
                  <label style="width: 100%;">Período:</label>
                  <input name="mes_ini" id="mes-ini" type="text" class="datepicker " placeholder="data inicial" style="text-align:center;width:70px;" readonly/>
                  <input name="mes_fim" id="mes-fim" type="text" class="datepicker " placeholder="data final" style="text-align:center;width:70px;" readonly/>
                  <br /><br />
                  <label style="width: 100%;">Opções:</label>
                  <a href="javascript://void(0);" title="" class="button basic" onClick="visualizar_lancamentos();" id="" style="padding:7px;width:100px;text-align:center">Gerar Lote</a>
                  <a href="javascript://void(0);" title="" class="button basic" onClick="DocumentosDownload();" id="" style="padding:7px;width:100px;text-align:center">Documentos</a>
                  <br>
                  <a href="javascript://void(0);" title="" class="button basic" onClick="RemessaHistorico();" id="" style="padding:7px;width:100px;text-align:center">Histórico</a>
                  <a href="clientesDetalhesConfig/<?php echo $clienteId?>" title="" class="button basic" onClick="" style="padding:7px;width:100px;text-align:center">Configurações</a>
                </span>
                
              </div>
            
            </form>
            
            <div class="formRow" id="div-lotes">
            
            </div>
    
          </div>
          
        </div>
           
      </div>

	</div>

<!-- ====== Fim do Palco ====== -->


<?php include("dialog_gerar_arquivo.php"); ?>

<form target="_blank" method="post" action="RemessaHistorico" id="formHistorico"> <!-- action="php/MPDF/examples/relatorios.php" -->
	<input type="hidden" name="funcao" value="RemessaHistorico"/>
	<input type="hidden" name="mes_ini" value="" id="historico-mes-ini"/>
	<input type="hidden" name="mes_fim" value="" id="historico-mes-fim"/>
	<input type="hidden" name="cf_id" value="" id="historico-cf"/>
 	<input type="hidden" name="cliente_id" value="<?php echo $clienteId;?>" id="cliente-id"/>
</form>