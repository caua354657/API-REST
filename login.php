<?php
    require('configuracoes/api_ativar.php');
    require('configuracoes/acoes.php');
    require('configuracoes/jwt.php');

    header('Content-Type: application/json');

    if(API_IS_ACTIVE)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $email = trim($_POST['email'] ?? null);
            $senha = $_POST['senha'] ?? null; 

            $sql = "select senha from usuarios where usuario = '$email'";
            $dados = $conexao->query($sql);
            $linha = $dados->fetch_assoc();
            $hash = $linha['senha'] ?? null;

            $usuario = usuarios::login($email); // chamo a funcao da classe usuarios

            if(!empty($email) && !empty($senha)) 
            {
                if($usuario == true)
                {
                    $token = json_web_token::gerar_token(
                        $usuario['id'],
                        $usuario['nome'],
                        $usuario['usuario'],
                        $usuario['categoria'],
                        $usuario['id_empresa']
                    );
                
                    if(password_verify($senha, $hash))
                    {
                            echo json_encode([
                                'status' => 200,
                                'mensagem' => 'Login realizado com sucesso',
                                'token' => $token,
                                'usuario' => [
                                    'id' => $usuario['id'],
                                    'nome' => $usuario['nome'],
                                    'email' => $usuario['usuario'],
                                    'categoria' => $usuario['categoria'],
                                    'id_empresa' => $usuario['id_empresa']
                                ]
                            ]);
                    }
                    else
                    {
                        http_response_code(400);
                        echo json_encode([
                                'status' => 400,
                                'message' => 'Senha Incorreta']
                        );
                        exit;
                    }
                }
                else
                {
                    http_response_code(401);
                    echo json_encode([
                            'status' => 401,
                            'message' => 'Usuário não encontrado']
                    );
                    exit;
                }
            }
            else
            {
                http_response_code(401);
                echo json_encode([
                        'status' => 401,
                        'message' => 'Campo E-mail e Senha obrigatório'
                ]);
                exit;
            }
        }
        else
        {
            http_response_code(405);
            echo json_encode([
                'status' => 405,
                'mensagem' => 'Método não é POST',
                'data_hora_resposta' => date('d-m-Y H:i:s')],
                JSON_PRETTY_PRINT
            );
        }
    }
    else
    {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'mensagem' => 'API Desligada',
            'data_hora_resposta' => date('d-m-Y H:i:s')], 
            JSON_PRETTY_PRINT);
    }

    $conexao->close();