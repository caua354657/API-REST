<?php
    require __DIR__ . "/../vendor/autoload.php";
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Firebase\JWT\ExpiredException;
    use Firebase\JWT\SignatureInvalidException;

    class json_web_token 
    {
        public static function gerar_token($id, $nome, $email, $categoria, $id_empresa) 
        {            
                return JWT::encode([
                    'id' => $id,
                    'nome' => $nome,
                    'usuario' => $email,
                    'categoria' => $categoria,
                    'id_empresa' => $id_empresa,
                    'exp' => time() + 3600], 
                    'minha_chave_secreta', 
                    'HS256'
                );
        }

        public static function validar_token() 
        {
            $header = getallheaders();

            if(!isset($header['Authorization'])) 
            {
                return['status' => 400,
                       'mensagem' => 'Token não fornecido'];
            }

            $header_completo = $header['Authorization']; 
            $array = explode(" ", $header_completo);

            //Verifica se o token está presente após o Bearer
            if(isset($array[1]) && !empty($array[1])) 
            {
                $token = $array[1];
            } 
            else 
            {
                return['status' => 400,
                       'mensagem' => 'Token ausente no cabeçalho Authorization'];
            }

            try 
            {
                $decodificado = JWT::decode($token, new Key('minha_chave_secreta', 'HS256'));

                return['status' => 200,
                       'mensagem' => 'Token válido',
                       'payload' => $decodificado];
            }
            catch(ExpiredException $erro) 
            {
                return['status' => 401,
                       'mensagem' => 'Token expirado'];
            }
            catch(SignatureInvalidException $erro) 
            {
                return['status' => 401,
                       'mensagem' => 'Assinatura inválida'];
            }
            catch(Exception $erro) 
            {
                return['status' => 400,
                       'mensagem' => 'Token inválido'];
            }
        }
    }