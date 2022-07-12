<?php
$nomeArquivoTXT = $_GET['download'];
if(isset($nomeArquivoTXT)){
header('Content-Disposition: attachment; filename="'.$nomeArquivoTXT.'"');
readfile('txt/'.$nomeArquivoTXT);
/* ==== Exclusão de arquivos e pastas TXT ==== */
array_map('unlink', glob("txt/*.txt"));//remove todos os TXT da pasta criada temporáriamente
}else{ echo "Não existem arquivos para download"; }
?>