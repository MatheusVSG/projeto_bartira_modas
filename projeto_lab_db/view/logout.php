<?php
// Inicia a sessão ou continua a sessão atual
session_start();

// Remove todas as variáveis da sessão atual
session_unset();

// Destrói a sessão, apagando os dados armazenados no servidor
session_destroy();

// Redireciona o usuário para a página index.php que está um nível acima da pasta atual
header("Location: ../index.php");

// Encerra a execução do script para garantir que o redirecionamento ocorra imediatamente
exit();
