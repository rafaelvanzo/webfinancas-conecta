<?php
$nomeArquivoTXT = $_GET['download'];
if(isset($nomeArquivoTXT)){
header('Content-Disposition: attachment; filename="'.$nomeArquivoTXT.'"');
readfile('txt/'.$nomeArquivoTXT);
/* ==== Exclus�o de arquivos e pastas TXT ==== */
array_map('unlink', glob("txt/*.txt"));//remove todos os TXT da pasta criada tempor�riamente
}else{ echo "N�o existem arquivos para download"; }
?>