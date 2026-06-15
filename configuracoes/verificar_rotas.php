<?php
    require_once("api_ativar.php");

    $rota = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $rota = str_replace('/Farol-Software-Projetos/api_rest_php/public/v1/endpoints', '', $rota); // remove uma parte do caminho
    
    $metodo = $_SERVER['REQUEST_METHOD'];

    if(API_IS_ACTIVE) 
    {
        if($metodo == 'POST') 
        {
            switch($rota) 
            {
                case 'post/cargos.php':
                    require __DIR__ . 'post/cargos.php';
                    break;

                case 'post/tipos.php':
                    require __DIR__ . 'post/tipos.php';
                    break;

                case 'post/empresas.php':
                    require __DIR__ . 'post/empresas.php';
                    break;

                case 'post/pessoas.php':
                    require __DIR__ . 'post/pessoas.php';
                    break;

                case 'post/usuarios.php':
                    require __DIR__ . 'post/usuarios.php';
                    break;

                default:
                    echo json_encode([
                        'status' => 404,
                        'mensagem' => 'Rota POST não encontrada',
                        'data_hora_resposta' => date('d-m-Y H:i:s')]
                    );
                    exit;
            }
        } 
        elseif($metodo == 'GET') 
        {
            switch($rota) 
            {
                case 'get/cargos.php':
                    require __DIR__ . 'get/cargos.php';
                    break;

                case 'get/tipos.php':
                    require __DIR__ . 'get/tipos.php';
                    break;

                case 'get/empresas.php':
                    require __DIR__ . 'get/empresas.php';
                    break;

                case 'get/pessoas.php':
                    require __DIR__ . 'get/pessoas.php';
                    break;

                case 'get/usuarios.php':
                    require __DIR__ . 'get/usuarios.php';
                    break;

                default:
                    echo json_encode([
                        'status' => 404,
                        'mensagem' => 'Rota GET não encontrada',
                        'data_hora_resposta' => date('d-m-Y H:i:s')]
                    );
                    exit;
            }
        } 
        elseif($metodo == 'PUT')
        {
            switch($rota) 
            {
                case '/public/v1/endpoints/put/retorno_alterar_cargos.php':
                    require __DIR__ . '/../public/v1/endpoints/put/retorno_alterar_cargos.php';
                    break;

                case 'put/tipos.php':
                    require __DIR__ . 'put/tipos.php';
                    break;

                case 'put/empresas.php':
                    require __DIR__ . 'put/empresas.php';
                    break;

                case 'put/pessoas.php':
                    require __DIR__ . 'put/pessoas.php';
                    break;

                case 'put/usuario.php':
                    require __DIR__ . 'put/usuario.php';
                    break;

                default:
                    echo json_encode([
                        'status' => 404,
                        'mensagem' => 'Rota PUT não encontrada',
                        'data_hora_resposta' => date('d-m-Y H:i:s')]
                    );
                    exit;
            }
        }
        elseif($metodo == 'DELETE')
        {
            switch($rota) 
            {
                case 'delete/cargos.php':
                    require __DIR__ . 'delete/cargos.php';
                    break;

                case 'delete/tipos.php':
                    require __DIR__ . 'delete/tipos.php';
                    break;

                case 'delete/empresas.php':
                    require __DIR__ . 'delete/empresas.php';
                    break;

                case 'delete/pessoas.php':
                    require __DIR__ . 'delete/pessoas.php';
                    break;

                case 'delete/usuarios.php':
                    require __DIR__ . 'delete/usuarios.php';
                    break;

                default:
                    echo json_encode([
                        'status' => 404,
                        'mensagem' => 'Rota DELETE não encontrada',
                        'data_hora_resposta' => date('d-m-Y H:i:s')]
                    );
                    exit;
            }
        }
        else 
        {
            echo json_encode([
                        'status' => 405,
                        'mensagem' => 'Rota não encontrada',
                        'data_hora_resposta' => date('d-m-Y H:i:s')]
                    );
            exit;
        }
    } 
    else 
    {
        echo json_encode([
            'status' => 400,
            'mensagem' => 'API Desligada',
            'data_hora_resposta' => date('d-m-Y H:i:s')], 
            JSON_PRETTY_PRINT);
    }
