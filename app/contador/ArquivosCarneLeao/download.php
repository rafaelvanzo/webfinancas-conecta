<?php
$nomeArquivoTXT = $_GET['download'];
if(isset($nomeArquivoTXT)){
header('Content-Disposition: attachment; filename="'.$nomeArquivoTXT.'"');
readfile($nomeArquivoTXT);
/* ==== Exclus�o de arquivos e pastas TXT ==== */
//array_map('unlink', glob('*.DEC'); Remover todos os arquivos da pasta.
unlink($nomeArquivoTXT);
}else{ echo "N�o existem arquivos para download"; }
?>