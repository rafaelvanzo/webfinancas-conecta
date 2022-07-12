<?php
 //Incluir a classe excelwriter
 include("excel.Class.php");

 //Você pode colocar aqui o nome do arquivo que você deseja salvar.
 $excel=new ExcelWriter("plano_de_contas.xls");

 if($excel==false){
     echo $excel->error;
 }

 //Escreve o nome dos campos de uma tabela
 $myArr=array('CODIGO', 'NOME DO PLANO DE CONTAS', 'FLUXO DE CAIXA', 'DRE');
 $excel->writeLine($myArr);

 //Seleciona os campos de uma tabela
 $conn = mysql_connect("mysql.webfinancas.com", "webfinancas09", "W2BSISTEMAS") or die ('Não foi possivel conectar ao banco de dados! Erro: ' . mysql_error());
 if($conn)
 {
     mysql_select_db("webfinancas09", $conn);
 }
 $consulta = "select cod_conta, nome, clfc_fc, clfc_dre from plano_contas order by cod_conta";
 $resultado = mysql_query($consulta);
 if($resultado==true){
     while($linha = mysql_fetch_array($resultado)){
         
         if($linha['clfc_fc'] != 0){ $fluxoCaixa = $linha['clfc_fc']; }else{ $fluxoCaixa = ''; }
         if($linha['clfc_dre'] != 0){ $DRE = $linha['clfc_dre']; }else{ $DRE = ''; }
          
         $myArr=array(utf8_decode($linha['cod_conta']), utf8_decode($linha['nome']), $fluxoCaixa, $DRE);
         $excel->writeLine($myArr);
     }
 }


 $excel->close();
 echo "O arquivo foi salvo com sucesso. <a href=\"plano_de_contas.xls\">plano_de_contas.xls</a>";

 ?>