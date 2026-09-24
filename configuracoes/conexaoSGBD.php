<?php
$servername = "seu_servidor";
$username = "seu_banco";
$password = "sua_senha";
$schema = "seu_banco";

// Create connection
$conexao = new mysqli($servername, $username, $password, $schema);

// Check connection
if($conexao->connect_error) 
{
  die("Falha na Conexao: " . $conexao->connect_error);
}
//echo "Conexao Efetuada com Sucesso.";

$conexao->set_charset("utf8mb4");
?>
