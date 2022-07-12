<?php
$nomeArquivoTXT = $_GET['download'];
if(isset($nomeArquivoTXT)){
header('Content-Disposition: attachment; filename="'.$nomeArquivoTXT.'"');
readfile($nomeArquivoTXT);
/* ==== Exclusão de arquivos e pastas TXT ==== */
//array_map('unlink', glob('*.DEC'); Remover todos os arquivos da pasta.
unlink($nomeArquivoTXT);
}else{ echo "Não existem arquivos para download"; }
?>