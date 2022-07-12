<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Arquivo de Remessa</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>
 
        </div>
    </div>    
    <!-- Fim título -->  
    

	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />
   
    <!-- Botões -->
        <div class="wrapper">        	    
	      <!--  <a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-conta-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Nova conta</span></a>
          <a href="conciliacao" title="" class="button blueB" style="margin: 5px;"><img src="images/icons/light/transfer.png" alt="" class="icon"/><span>Conciliação</span></a> -->
          <!--<a href="importarLancamentos" title="" class="button dblueB" style="margin:5px;"><img src="images/icons/light/arrowDown.png" alt="" class="icon"/><span>Importação</span></a>-->
          <a href="arquivosRemessa" title="" class="button greyishB" style="margin: 5px;" id="opener-arquivo-remessa-incluir"><img src="images/icons/light/transfer.png" alt="" class="icon"/><span>Nova Remessa</span></a>
        </div>

    
 <!--   <div class="line"></div>
    
    <!-- Main content wrapper -->
    <div class="wrapper">
    
    		
        <!-- =================== Palco =================== -->

 <!-- Dynamic table -->
        <div class="widget">
            <div id="contas">
              <table cellpadding="0" cellspacing="0" border="0" class="display tblRemessas sTable">
              <thead>
              <tr style="border-bottom: 1px solid #e7e7e7;">                   
                  <th style="display:none;">Id</th>
                      <th>Data</th>
                      <th>Nome</th>
                      <th>Banco</th>
                      <th>Nº Remessa</th>
                      <th>Qtd. Boletos</th>
                      <th>Valor Total</th>
                      <th width="60">Arquivo</th>
              </tr>
              </thead>
              <tbody id="listarRemessa">
              
              <?php
              $array_arquivo = $db->fetch_all_array("select id, date_format(dt_cadastro, '%d/%m/%Y') as dt_cadastro, nome, conta_id, banco_id, numero_remessa, valor
                                                    from boletos_remessa
                                                    order by id desc");
              foreach($array_arquivo as $arquivo){ 
                
                 $nome_banco = $db->fetch_assoc("SELECT nome FROM bancos WHERE id =".$arquivo['banco_id']);
                 $qtd_boletos = $db->numRows("SELECT id FROM boletos WHERE remessa_id =".$arquivo['id']);
							
                echo '
								<tr class="gradeA odd"" id="row',$arquivo['id'],'">
                                    <td style="display:none;">',$arquivo['id'],'</td>
								    <td align="center">',$arquivo['dt_cadastro'],'</td> 
                                    <td align="left">',$arquivo['nome'],'</td> 
                                    <td align="left">',$nome_banco['nome'],'</td>
                                    <td align="center">',$arquivo['numero_remessa'],'</td> 
                                    <td align="center">',$qtd_boletos,'</td> 
                                    <td align="right">',number_format($arquivo['valor'],2,',','.'),'</td> 
                                    <td><a href="javascript://" class="button brownB" style="margin: 1px;" onClick="javascript:gerarRemessaBotao(',$arquivo['conta_id'],',',$arquivo['banco_id'],',',$arquivo['id'],');"><span>Gerar</span></a></td> 
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
  
  <?php include("conta_dialogs.php");?>

 
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div>

<div id="dados"></div>
