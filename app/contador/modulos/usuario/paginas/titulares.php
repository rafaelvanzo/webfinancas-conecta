<!-- <script> alert(window.innerWidth); </script> --> 
 
 <!-- Título -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h2>Cadastros</h2>
               <br />
            </div>
				</div>
    </div>    
    <!-- Fim título -->  
    

   <!-- Breadcrumbs -->
<div class="wrapper">  
   <div class="bc" style="margin:2px 0 0 0;">
            <ul id="breadcrumbs" class="breadcrumbs">
                 <li class="current">
                      <a href="javascript://">Títulares</a>
                 </li>
            </ul>
	</div>  
</div> <!-- Fim Breadcrumbs -->


    <br />
   
    <!-- Botões -->
    <div class="wrapper">        	    
      <a href="javascript://void(0);" title="" class="button blueB" style="margin: 5px;" id="opener-titular-incluir"><img src="images/icons/light/user.png" alt="" class="icon"/><span>Novo Títular</span></a>
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
   
 <!-- Lista dos Títulares -->
 
  <div class="widget">
      <div class="title"><img src="images/icons/dark/user.png" alt="" class="titleIcon" /><h6>Títulares</h6></div>                          
      <div id="titulares">
        <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
        <thead>
        <tr>
        	<th width="60">Código</th>
          <th >Nome do títular</th>
          <th width="100">Números</th>
          <th width="150">Faturas</th>
          <th width="137">Opções</th>
        </tr>
        </thead>
        <tbody>
				<?php    
        $cliente_id = 1;
        $array_titulares_listar = $db->fetch_all_array("select t.id, t.nome, count(n.id) qtd_numeros from titulares t, numeros n where t.id = n.titular_id group by t.id");
        foreach($array_titulares_listar as $titulares){
          echo $titulares_listar = '
            <tr class="gradeA">
                <td align="center"><b>'.$titulares['id'].'</b></td>
                <td>'.$titulares['nome'].'</td>
                <td align="center">'.$titulares['qtd_numeros'].'</td>
                <td>
                  <select class="faturasDownload" style="width: auto; margin-bottom: -8px;">
                    <option value="" selected="selected">SELECIONE O MÊS</option>
          ';
          $i = 1;
          $array_arquivos = array(array("",""));
          $ano = date('Y');
          while($i<=12){
            $arquivo = "modulos/faturas/arquivos_pdf/cliente_".$cliente_id."/titular_12/".$i."_".$ano."/DETALHAMENTO_".$i."_".$ano.".zip";//$array_pastas = glob("../../faturas/arquivos_pdf/cliente_".$cliente_id."/titular_".$titulares[id]."/");
            if(file_exists($arquivo)){
              array_push($array_arquivos,array($i,$arquivo));
            }
            $i++;
          }
          unset($array_arquivos[0]);
					foreach($array_arquivos as $arquivo){
						$m = $arquivo[0];
						if($m == 1){ $mes = 'Janeiro';}
						elseif($m == 2){ $mes = 'Fevereiro';}
						elseif($m == 3){ $mes = 'Março';}
						elseif($m == 4){ $mes = 'Abril';}
						elseif($m == 5){ $mes = 'Maio';}
						elseif($m == 6){ $mes = 'Junho';}
						elseif($m == 7){ $mes = 'Julho';}
						elseif($m == 8){ $mes = 'Agosto';}
						elseif($m == 9){ $mes = 'Setembro';}
						elseif($m == 10){ $mes = 'Outubro';}
						elseif($m == 11){ $mes = 'Novembro';}
						else{ $mes = 'Dezembro';}
            echo '
              <option value="'.$arquivo[1].'">'.$mes.'</option>
            ';
          }
          echo '						
                  </select>				
                </td>
                <td>
                  <a href="numeros/'.$titulares['id'].'" title="Números" class="smallButton" style="margin: 5px;"><img src="images/icons/dark/phone3.png" alt=""></a>
                  <a href="javascript://void(0);" title="Editar" class="smallButton" style="margin: 5px;" onClick="titularesExibir(\''.$titulares['id'].'\')"><img src="images/icons/dark/pencil.png" alt=""></a>
                  <a href="'.$titulares['id'].'" title="Excluír" class="smallButton titularesExcluir" style="margin: 5px;"><img src="images/icons/dark/close.png" alt=""></a>
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
  
  <?php include("dialog_cadastros_titular_incluir.php"); ?>
  
  <?php include("dialog_cadastros_titular_editar.php"); ?>
   
  <!-- ====== *** Fim UI Dialogs *** ====== -->
 
	</div> 
</div>