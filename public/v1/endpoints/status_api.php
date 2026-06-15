<?php
    require_once __DIR__ . "/../../../configuracoes/api_ativar.php";
    require_once __DIR__ . "/../../../configuracoes/resposta_status_api.php";

    if(API_IS_ACTIVE)
    {
        echo resposta::json(200, 'API ligada');
    }
    else
    {
        http_response_code(400);
        echo resposta::json(400, 'API desligada');
    }