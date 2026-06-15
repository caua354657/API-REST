<?php
$servername = "mysql.escola25dejulho.com.br";
$username = "escola25dejulh93";
$password = "aula2024";
$schema = "escola25dejulh93";

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