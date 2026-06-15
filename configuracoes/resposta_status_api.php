<?php
    class resposta
    {
        public static function json($status = 200, $mensagem = "API ligada", $data = null)
        {
            header('Content-Type: application/json');

            if(API_IS_ACTIVE)
            {
                return json_encode([
                    'status' => $status, 
                    'mensagem' => $mensagem, 
                    'data_hora_resposta' => date('d-m-Y H:i:s')], 
                    JSON_PRETTY_PRINT);
            }
            else
            {
                return json_encode([
                    'status' => 400, 
                    'mensagem' => 'API desligada', 
                    'data_hora_resposta' => date('d-m-Y H:i:s')],  
                    JSON_PRETTY_PRINT);
            }
        }
    }