<?php
    require("conexaoSGBD.php");

    class cargos
    {
        //cadastrar cargo
        public static function cadastro_cargo($nome)
        {
            global $conexao;
            $sql = "insert into cargos(nome) values ('$nome')";
            return $conexao->query($sql);
        }

        //pegar ultimo registro de cargo
        public static function mostrar_cargo()
        {
            global $conexao;
            $sql = "select * from cargos order by id desc limit 1";
            $resultado = $conexao->query($sql);

            if($resultado->num_rows > 0)
            {
                return $resultado->fetch_assoc()['nome'];
            }
            else
                return null;
        }

        //mostrar todos cargos cadastrados
        public static function cargos_cadastrados()
        {
            global $conexao;
            $sql = "select * from cargos";
            $dados = $conexao->query($sql);
            $array = [];

            if($dados == true)
            {
                while($row = $dados->fetch_assoc())
                {
                    $array[] = $row;
                }
            }
            return $array; 
        }

        //alterar cargo
        public static function alterar_cargo($id, $nome)
        {
            global $conexao;
            $id = intval($id);
            $sql = "update cargos set nome = '$nome' where id = $id";
            return $conexao->query($sql);
        }

        //mostrar a alteracao ja realizada
        public static function mostrar_alterado_cargo($id)
        {
            global $conexao;
            $sql = "select nome from cargos where id = $id";
            $resu = $conexao->query($sql);
            
            if($resu->num_rows > 0)
            {
                return $resu->fetch_assoc()['nome'];
            }
            else
                return null;
        }

        //excluir cargo
        public static function excluir_cargo($id)
        {
            global $conexao;
            $id = intval($id);
            $sql = "delete from cargos where id = '$id'";
            $conexao->query($sql);

            return $conexao->affected_rows > 0;
        }
    }

    class tipos
    {
        //cadastrar tipo
        public static function cadastro_tipo($nome, $controla_espacos)
        {
            global $conexao;
            $sql = "insert into tipo_cadastro(nome, controla_espacos) values('$nome', '$controla_espacos')";
            return $conexao->query($sql);
        }

        //pegar último registro de tipo
        public static function mostrar_tipo()
        {
            global $conexao;
            $sql = "select * from tipo_cadastro order by id desc limit 1";
            $resultados = $conexao->query($sql);
            $array = [];
            
            if($resultados->num_rows > 0)
            {
                $rowline = $resultados->fetch_assoc();
                $array[] = $rowline;
                return $array;
            }
            else
                return null;
        }

        //mostrar todos os tipos cadastrados
        public static function tipos_cadastrados()
        {
            global $conexao;
            $sql = "select * from tipo_cadastro";
            $informacoes = $conexao->query($sql);
            $array = [];

            if($informacoes == true)
            {
                while($line = $informacoes->fetch_assoc())
                {
                    $array[] = $line;
                }
            }
            return $array;
        }

        //alterar tipos
        public static function alterar_tipo($id, $nome)
        {
            global $conexao;
            $id = intval($id);
            $sql = "update tipo_cadastro set nome = '$nome' where id = $id";
            return $conexao->query($sql);
        }

        //mostrar a alteracao ja realizada
        public static function mostrar_alterado_tipo($id)
        {
            global $conexao;
            $sql = "select nome from tipo_cadastro where id = $id";
            $resposta = $conexao->query($sql);
            
            if($resposta->num_rows > 0)
            {
                return $resposta->fetch_assoc()['nome'];
            }
            else
                return null;
        }

        //excluir tipo
        public static function excluir_tipos($id)
        {
            global $conexao;
            $id = intval($id);
            $sql = "delete from tipo_cadastro where id = '$id'";
            $conexao->query($sql);

            return $conexao->affected_rows > 0;
        }
    }

    class empresas
    {
        //cadastro enpresas
        public static function cadastro_empresas($fantasia, $razao, $cnpj, $tipo_cadastro)
        {
            global $conexao;
            $ano = date('Y');
            $importado = "Não";
            $sql = "insert into empresas(ano, nome_fantasia, razao_social, cnpj, tipo_cadastro, importado) values('$ano','$fantasia','$razao', '$cnpj', '$tipo_cadastro', '$importado')";
            return $conexao->query($sql);
        }

        //mostrar último registro de empresas
        public static function mostrar_empresa()
        {
            global $conexao;
            $sql = "select empresas.id, empresas.ano, empresas.nome_fantasia, empresas.razao_social, empresas.cnpj, tipo_cadastro.nome as tipo_cadastro, empresas.importado from empresas join tipo_cadastro on tipo_cadastro.id = empresas.tipo_cadastro where empresas.tipo_cadastro = tipo_cadastro.id order by id desc limit 1";
            $informacao = $conexao->query($sql);
            $array = [];

            if($informacao->num_rows > 0)
            {
                $rowsline = $informacao->fetch_assoc();
                $array[] = $rowsline;
                return $array;
            }
            else
                return null;
        }

        //mostrar todas as empresas
        public static function empresas_todas()
        {
            global $conexao;
            $sql = "select * from empresas";
            $information = $conexao->query($sql);
            $array = [];

            if($information == true)
            {
                while($linhas = $information->fetch_assoc())
                {
                    $array[] = $linhas;
                }
            }
            return $array;
        }

        //mostrar todas as empresas que o usuario pertence
        public static function empresas_usuario($id_empresa)
        {
            global $conexao;
            $id = intval($id_empresa);
            $sql = "select * from empresas where id = '$id'";
            $information = $conexao->query($sql);
            $array = [];

            if($information == true)
            {
                while($linhas = $information->fetch_assoc())
                {
                    $array[] = $linhas;
                }
            }
            return $array;
        }

        //alterar empresas
        public static function alterar_empresa($id, $nome_fantasia, $razao_social, $tipo, $cnpj, $importado)
        {
            global $conexao;
            $id = intval($id);
            $sql = "update empresas set nome_fantasia = '$nome_fantasia', razao_social = '$razao_social', tipo_cadastro = '$tipo', cnpj = '$cnpj', importado = '$importado' where id = $id";
            return $conexao->query($sql);
        }

        //mostrar a alteracao ja realizada
        public static function mostrar_alterado_empresa($id)
        {
            global $conexao;
            $sql = "select * from empresas where id = $id";
            $r = $conexao->query($sql);
            
            if($r->num_rows > 0)
            {
                $l = $r->fetch_assoc();
                return[
                        'ano' => $l['ano'],
                        'nome' => $l['nome_fantasia'],
                        'razao' => $l['razao_social'],
                        'tipo' => $l['tipo_cadastro'],
                        'cnpj'=> $l['cnpj'],
                        'importado' => $l['importado']
                    ];
            }
            else
                return null;
        }

        //excluir empresas
        public static function excluir_empresas($id)
        {
            global $conexao;
            $id = intval($id);
            $sql = "delete from empresas where id = '$id'";
            $conexao->query($sql);

            return $conexao->affected_rows > 0;
        }
    }

    class pessoas
    {
        //cadastro pessoas
        public static function cadastro_pessoas($nome, $cpf, $rg, $telefone, $foto, $empresa_id, $cargo_id, $ingresso, $enviado_catraca, $importado, $impresso)
        {
            global $conexao;
            $ano = date("Y");
            $data_hora = date("Y-m-d H:i:s");
            $sql = "insert into pessoas(ano, nome, cpf, rg, telefone, foto, empresa_id, cargo_id, ingresso_permanente, enviado_catraca, importado, impresso, impresso_em, criado_em, atualizado_em, excluido_em, cad_biometria_em, env_catraca_em) values('$ano', '$nome', '$cpf', '$rg', '$telefone', '$foto', '$empresa_id', '$cargo_id', '$ingresso', '$enviado_catraca', '$importado', '$impresso', current_timestamp, current_timestamp, current_timestamp, current_timestamp, current_timestamp, current_timestamp)";
            return $conexao->query($sql);
        }

        //mostrar último registro pessoas
        public static function mostrar_pessoas()
        {
            global $conexao;
            $sql = "select pessoas.id, pessoas.nome, empresas.razao_social as empresa, cargos.nome as cargo, pessoas.foto, pessoas.cpf, pessoas.rg, pessoas.telefone, pessoas.ingresso_permanente, pessoas.enviado_catraca, pessoas.importado, pessoas.impresso, pessoas.criado_em, pessoas.atualizado_em, pessoas.excluido_em, pessoas.cad_biometria_em, pessoas.env_catraca_em from pessoas join empresas on empresas.id = pessoas.empresa_id join cargos on cargos.id = pessoas.cargo_id order by pessoas.id desc limit 1";
            $resulta = $conexao->query($sql);
            $array = [];

            if($resulta->num_rows > 0)
            {
                $lin = $resulta->fetch_assoc();
                $array[] = $lin;
                return $array;
            }
            else 
                return null;
        }

        //mostrar todas as pessoas
        public static function pessoas_todas()
        {
            global $conexao;
            $sql = "select * from pessoas";
            $informations = $conexao->query($sql);
            $array = [];

            if($informations == true)
            {
                while($rows = $informations->fetch_assoc())
                {
                    $array[] = $rows;
                }
            }
            return $array;
        }

        //mostrar todas as pessoas cadastradas com mesmo id_empresa que o usuario autenticado
        public static function pessoas_usuario($id_empresa)
        {
            global $conexao;
            $id = intval($id_empresa);
            $sql = "select * from pessoas where empresa_id = '$id'";
            $informations = $conexao->query($sql);
            $array = [];

            if($informations == true)
            {
                while($rows = $informations->fetch_assoc())
                {
                    $array[] = $rows;
                }
            }
            return $array;
        }

        //alterar pessoas
        public static function alterar_pessoa($id, $nome, $cpf, $rg, $telefone, $foto, $empresa_id, $cargo_id, $ingresso, $enviado_catraca, $importado, $impresso)
        {
            global $conexao;
            $id = intval($id);
            $sql = "update pessoas set nome = '$nome', cpf = '$cpf', rg = '$rg', telefone = '$telefone', foto = '$foto', empresa_id = '$empresa_id', cargo_id = '$cargo_id', ingresso_permanente = '$ingresso', enviado_catraca = '$enviado_catraca', importado = '$importado', impresso = '$impresso' where id = $id";
            return $conexao->query($sql);
        }

        //mostrar a alteracao ja realizada
        public static function mostrar_alterado_pessoa($id)
        {
            global $conexao;
            $sql = "select * from pessoas where id = $id";
            $re = $conexao->query($sql);
            
            if($re->num_rows > 0)
            {
                $li = $re->fetch_assoc();
                return[
                        'nome' => $li['nome'],
                        'cpf' => $li['cpf'],
                        'rg' => $li['rg'],
                        'telefone' => $li['telefone'],
                        'foto' => $li['foto'],
                        'empresa_id' => $li['empresa_id'],
                        'cargo_id' => $li['cargo_id'],
                        'ingresso' => $li['ingresso_permanente'],
                        'enviado_catraca' => $li['enviado_catraca'],
                        'importado' => $li['importado'],
                        'impresso' => $li['impresso'],
                    ];
            }
            else
                return null;
        }

        //excluir pessoas
        public static function excluir_pessoas($id)
        {
            global $conexao;

            $sql = "delete from pessoas where id = $id";
            $conexao->query($sql);

            return $conexao->affected_rows > 0;
        }
    }

    class usuarios
    {
        //para login
        public static function login($email)
        {
            global $conexao;
            $sql = "select * from usuarios where usuario = '$email'";
            $dado = $conexao->query($sql);
            return $dado->fetch_assoc();
        }

        //cadastro usuarios
        public static function cadastro_usuarios($nome, $email, $senha, $categoria, $id_empresa)
        {
            global $conexao;
            $sql = "insert into usuarios(nome, usuario, senha, categoria, id_empresa) values('$nome', '$email', '$senha', '$categoria', '$id_empresa')";
            return $conexao->query($sql);
        }

        //pegar último registro de usuarios
        public static function mostrar_usuarios()
        {
            global $conexao;
            $sql = "select usuarios.id, usuarios.nome, usuarios.usuario, usuarios.senha, usuarios.categoria, empresas.razao_social as empresa from usuarios join empresas on empresas.id = usuarios.id_empresa order by usuarios.id desc limit 1";
            $resul = $conexao->query($sql);
            $array = [];
            
            if($resul->num_rows > 0)
            {
                $rowslin = $resul->fetch_assoc();
                $array[] = $rowslin;
                return $array;
            }
            else
                return null;
        }

        //mostrar todos usuarios
        public static function usuarios_todos()
        {
            global $conexao;
            $sql = "select * from usuarios";
            $result = $conexao->query($sql);
            $array = []; //criei um vetor
            
            if($result == true)
            {
                while($linha = $result->fetch_assoc())
                {
                    $array[] = $linha;
                }
            }
            return $array;
        }

        //mostrar todos usuarios cadastrados com o mesmo id do logado
        public static function usuarios_mesma_empresa($id_empresa)
        {
            global $conexao;
            $id = intval($id_empresa);
            $sql = "select * from usuarios where id_empresa = '$id'";
            $result = $conexao->query($sql);
            $array = []; //criei um vetor
            
            if($result == true)
            {
                while($linha = $result->fetch_assoc())
                {
                    $array[] = $linha;
                }
            }
            return $array;
        }

        //alterar tipos
        public static function alterar_usuario($id, $usuario, $nome, $senha, $categoria, $id_empresa)
        {
            global $conexao;
            $id = intval($id);
            $senhahash = password_hash($senha, PASSWORD_DEFAULT);
            $id_empresa = intval($id_empresa);
            $sql = "update usuarios set usuario = '$usuario', nome = '$nome', senha = '$senhahash', categoria = '$categoria', id_empresa = '$id_empresa' where id = $id";
            return $conexao->query($sql);
        }

        //mostrar a alteracao ja realizada
        public static function mostrar_alterado_usuario($id)
        {
            global $conexao;
            $sql = "select * from usuarios where id = $id";
            $res = $conexao->query($sql);
            
            if($res->num_rows > 0)
            {
                $linhass = $res->fetch_assoc();
                return[
                        'usuario' => $linhass['usuario'],
                        'nome' => $linhass['nome'],
                        'senha' => $linhass['senha'],
                        'categoria' => $linhass['categoria'],
                        'id_empresa'=> $linhass['id_empresa']
                    ];
            }
            else
                return null;
        }

        //excluir usuários
        public static function excluir_usuarios($id)
        {
            global $conexao;
            $id = intval($id);
            $sql = "delete from usuarios where id = '$id'";
            $conexao->query($sql);

            return $conexao->affected_rows > 0;
        }
    }