<?php require("php/db_conexao.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Incluir Potfólio</title>
</head>

<body>


<form action="?incluir" method="post">

Nome:<input type="text" name="nome" /><br />
Link:<input type="text" name="link" /><br />
Categoria:<input type="text" name="categoria" /> 1- Sistema &nbsp; 2- Site &nbsp; 3- Site Acessível<br /><br />
Imagem 01:<input type="file" name="imagem1" /> (447 x 447 px)<br />
Imagem 02:<input type="file" name="imagem2" /> (447 x 447 px)<br />
Imagem 03:<input type="file" name="imagem3" /> (447 x 447 px)<br /><br />

Tecnologia: <br />
    
               <?php
							  $portfoliotec = $db->fetch_all_array("select * from portfolio_lista_tecnologia");
											foreach($portfoliotec as $tec){ $c += 1;
								?>

										<input type="checkbox" name="<?php echo "tec".$c ?>" value="<?php echo $tec['id']; ?>" /><?php echo $tec['tecnologia']; ?>

								<?php }	?>
<br /><br />
Descrição:<br />
<textarea name="descricao" cols="40" rows="20"></textarea>

<br />

	<input type="submit" value="Salvar" />

</form>

<?php
if(isset($_GET['incluir'])){
	 $nome = $_POST['nome'];
	 $link = $_POST['link'];
	 $categoria = $_POST['categoria']; 

/*
$i = 1;
while($i <= 8){	
	echo $tecnologia.$i = $_POST['tec'.$i];
$i++;
} 
*/
	
	echo $descricao = $_POST['descricao'];
	
	/* Portfolio */ 
 $array_portfolio = array("nome" => $nome, "link" => $link, "categoria" => $categoria, "descricao" => $descricao);	
	$id_portfolio = $db->query_insert(portfolio,$array_portfolio);
	
 /* Portfolio Imagem */ 
 if(!empty($_POST['imagem1'])){
 	
	$imagem1 = $_POST['imagem1'];
	
 $array_portfolio_imagens1 = array("imagem" => $imagem1, "id_portfolio" => $id_portfolio);	
	$db->query_insert(portfolio_imagens,$array_portfolio_imagens1);
	
 }
	
	if(!empty($_POST['imagem2'])){
 	
	$imagem2 = $_POST['imagem2'];

	 $array_portfolio_imagens2 = array("imagem" => $imagem2, "id_portfolio" => $id_portfolio);	
	$db->query_insert(portfolio_imagens,$array_portfolio_imagens2);

		}	
	
	 if(!empty($_POST['imagem3'])){
 	
	$imagem3 = $_POST['imagem3'];
	
	 $array_portfolio_imagens3 = array("imagem" => $imagem3, "id_portfolio" => $id_portfolio);	
	$db->query_insert(portfolio_imagens,$array_portfolio_imagens3);
	 
	 }
	 
	/*Portfolio Tecnologia */
	$i = 1;
	while($i <= 8){
		if(!empty($_POST['tec'.$i])){
			
			$tecnologia = $_POST['tec'.$i];
	 			$array_portfolio_tec = array("tecnologia" => $tecnologia, "id_portfolio" => $id_portfolio);	
				$db->query_insert(portfolio_tecnologia,$array_portfolio_tec);
		
		}		
		$i++;
	}
	
echo "<script> location.href='adm.php'; </script>"; 
	
}
?>


</body>
</html>