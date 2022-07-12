<!-- <script> alert(window.innerWidth); </script> --> 

 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Centro de Custo</h2>
               <!-- <span>Do your layouts deserve better than Lorem Ipsum.</span> -->
            </div>

 <!-- === Cotação === -->      
 <?php //include("modulos/cambio/paginas/cambio.php"); ?>
 <!-- === Fim Cotação === --> 
 
        </div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs 
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="">
                      <a href="javascript://" style="cursor: default;">Geral</a>
	               </li>
                 <li class="current">
                      <a href="javascript://" style="cursor: default;">Centro de Responsabilidade</a>
                 </li>
            </ul>
	</div>  
</div> Fim Breadcrumbs -->

	  <div class="wrapper">
      <div class="divider">
      	<span></span>
      </div>
    </div>

    <br />
   
    <!-- Botões -->
        <div class="wrapper">        	    
	        <a href="#" title="" class="button greenB" style="margin: 5px;" id="opener-centro-resp-incluir"><img src="images/icons/light/add.png" alt="" class="icon"/><span>Novo Centro</span></a>
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

 <!-- Dynamic table -->
        <div class="widget">
            <div id="centroResp">
              <table cellpadding="0" cellspacing="0" border="0" class="display tblCentroResp">
              <thead>
                <tr style="border-bottom: 1px solid #e7e7e7;">
			    <th style="display:none;"></th>
			    <th> 
				    <table width="100%">
                        <tr>
						    <td>Descrição</td>
						    <td width="60">Opções</td>
                        </tr>
				    </table>
			    </th> 
    	        </tr>
              </thead>
              <tbody>
              
              <?php
							//ordena centro de responsabilidade
							$nivel = $db->fetch_assoc("select max(nivel) nivel from centro_resp");
							$nivel = $nivel['nivel'];
							if($nivel!=''){
	
								$col_ordem = "";
								$i=1;
								while($i<=$nivel){
									$col_ordem.= 'ordem'.$i.' smallint(2),';
									$i++;
								}
								$db->query("
									CREATE TEMPORARY TABLE ctr_temp (
										id int(11),
										cod_centro varchar(20),
										nome varchar(50),
										".$col_ordem."
										nivel smallint(2),
										posicao smallint(4)
									) ENGINE=MEMORY
								");
							
								$array_centro_resp = array();
								$arr_ctr_n1 = $db->fetch_all_array('select id, cod_centro, nome, nivel, posicao from centro_resp where nivel = 1 and cod_centro > 0');
								foreach($arr_ctr_n1 as $ctr_n1){
									$arr_cod_centro = explode('.',$ctr_n1['cod_centro']);
									$i=1;
									while($i<=count($arr_cod_centro)){
										$ctr_n1['ordem'.$i] = $arr_cod_centro[$i-1];
										$i++;
									}
									$db->query_insert('ctr_temp',$ctr_n1);
									$arr_filhos = $db->fetch_all_array("select id, cod_centro, nome, nivel, posicao from centro_resp where centro_pai_id = ".$ctr_n1['id']);
									$hasFilho = count($arr_filhos);
									while($hasFilho){
										$arr_centro_filho_id = array();
										foreach($arr_filhos as $filho){
											$arr_cod_centro = explode('.',$filho['cod_centro']);
											$i=1;
											while($i<=count($arr_cod_centro)){
												$filho['ordem'.$i] = $arr_cod_centro[$i-1];
												$i++;
											}
											$db->query_insert('ctr_temp',$filho);
											$arr_centro_filho_id[] = $filho['id'];
										}
										$str_centro_filho_id = join(',',$arr_centro_filho_id);
										$arr_filhos = $db->fetch_all_array("select id, cod_centro, nome, nivel, posicao from centro_resp where centro_pai_id in (".$str_centro_filho_id.")");
										$hasFilho = count($arr_filhos);
									}
								}
							
								$col_ordem = "";
								$i=1;
								while($i<=$nivel){
									$col_ordem.= 'ordem'.$i.',';
									$i++;
								}
								$col_ordem = substr($col_ordem,0,strlen($col_ordem)-1);
								$arr_ctr_ordem = $db->fetch_all_array('select * from ctr_temp order by '.$col_ordem);
								foreach($arr_ctr_ordem as $centro){
									$array_centro_resp[] = $centro;
								}
								$db->query('drop table ctr_temp');
								//fim ordena centro de responsabilidade

								$cont = 0;
								foreach($array_centro_resp as $centro_resp){
									$espc = $centro_resp['nivel'] * 10;  $espc = $espc.'px'; 
									if($centro_resp['nivel'] == 1){ $strong = 'bold;'; $fontN = '16px;';  }else{ $strong = 'normal;'; $fontN = '12px;'; }
									
									//$valor = $array_valores[$centro_resp['id']];
									if($centro_resp['tp_centro'] == '1'){ $tp_centro = 'Analítico'; }else{ $tp_centro = 'Sintético'; }
									$s = $db->fetch_assoc("select situacao from centro_resp where id = ".$centro_resp['id']); $sit = $s['situacao'];
									if($sit == '1'){ $situacao = '<span class="green">Ativo</span>'; $cor = ''; }else{ $situacao = '<span class="red">Inativo</span>'; $cor = 'color:#999;'; }
									echo '
									<tr class="gradeA" ',$cor,'>
									<td style="display:none;">',$cont,'</td>
									<td class="updates newUpdate" >
													
											<div class="uDate tbWF tipS" original-title="Nível" align="center" style="width:auto; padding-left:',$espc,'"> <span class="uDay ',$atrasado,'" style="font-size:',$fontN,$cor,' ">',$centro_resp['cod_centro'],' - </span></div>
												<span class="lDespesa tbWF" style="margin-top:2px; font-size:14px;">
													<a href="javascript://void(0);" style="cursor: default; font-size:',$fontN,' font-weight:',$strong,$cor,';" original-title="Descrição" class="tipS" >',$centro_resp['nome'],'</a>													
												</span>											
																
											<div class="tbWFoption">										
													<a href="',$centro_resp[id],'" original-title="Excluir" class="smallButton tipS redB btTBwf CentroRespExcluir"><img src="images/icons/light/close.png" width="10"></a>											
													<a href="javascript://void(0);" original-title="Editar" class="smallButton tipS btTBwf greyishB" onClick="centroRespExibir(',$centro_resp['id'],')"><img src="images/icons/light/pencil.png" width="10"></a> 
												',$situacao,'
												</div>
					
									</td> 
									</tr>									
									';
									$cont++;
								}
              }
              ?>
             
              </tbody>
              </table>
            </div>
        </div>
 
 	<!-- ====== Fim do Palco ====== -->
 
  <!-- ====== *** UI Dialogs *** ====== -->
  
  <?php require("centro_dialogs.php");?>
  
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div> 