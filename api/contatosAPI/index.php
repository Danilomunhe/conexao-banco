<?php

    //request - recebe dados do corpo da requisição (JSON, FORM/DATA, XML)
    //response - envia dados de retorno da api
    //$args - Permite receber dados de atributos na API
    // Import do arquivo autoload, que fará as instancias do slim

use Slim\Http\Response;

require_once('vendor/autoload.php');

    $app = new \Slim\App();

    // EndPoint: requisição para listar todos os contatos
    $app->get('/contatos', function($request, $response, $args){
        
        require_once("../modulo/config.php");
        require_once("../controller/controllerContatos.php");

        //solicita os dados para controller
        if($dados = listarContato()){
            //transforma um array em json
            if($dadosJSON = createJSON($dados)){

                //Caso exista dados a serem retornados, informações o statusCode 200 e enviamos um JSON com todos os dados encontrados
               return $response   -> withStatus(200)
                            ->withHeader('Content-type', 'application/json')
                            ->write($dadosJSON);
            }else{
                //retorna um statusCode que significa que a requisição foi aceita, porém sem conteudo de retorno 
                 return $response   -> withStatus(404);
                         
            }
        }
            
        
    
    });

    // EndPoint: requisição para listar contato pelo id
    $app->get('/contatos/{id}', function($request, $response, $args){
        $id = $args['id'];
         
        require_once("../modulo/config.php");
        require_once("../controller/controllerContatos.php");

        //solicita os dados para controller
        if($dados = buscarContato($id)){
            if(!isset($dados['idErro'])){
                //transforma um array em json
                if($dadosJSON = createJSON($dados)){

                    //Caso exista dados a serem retornados, informações o statusCode 200 e enviamos um JSON com todos os dados encontrados
                return $response   -> withStatus(200)
                                ->withHeader('Content-type', 'application/json')
                                ->write($dadosJSON);
                }            
            }else{
                     //retorna um statusCode que significa que a requisição foi aceita, porém sem conteudo de retorno 
                return $response    -> withStatus(404)
                                        -> withHeader('Content-type', 'application/json')
                                        -> write('{"Message": "Dados inválidos"}');
                }   
        }else{
            return $response -> withStatus(204);
        }
            
        
    });

    // EndPoint: requisição para inserir um novo contato
    $app->delete('/contatos/{id}', function($request, $response, $args){
        
        if(is_numeric($args['id'])){
            //recebe o id enviado no endpoint através da vaiavel id
            $id = $args['id'];
               
            //import dos arquivos
            require_once("../modulo/config.php");
            require_once("../controller/controllerContatos.php");

            //busca o nome da foto que a controller retornou
            if($dados = buscarContato($id)){

                //recebe o nome da foto que a controller retornou
                $foto = $dados['foto'];

                //Cria um array com o id e o nome da foto a ser enviada para controller excluir
                $arrayDados = array(
                    "id"   => $id,
                    "foto" => $foto
                );

                //chama a função de excluir o contato, encaminhando o array com o id e a foto
                $resposta = excluirContato($arrayDados);

                if(is_bool($resposta) && $resposta == true){
                    return $response    -> withStatus(200)
                                        -> withHeader('Content-type', 'application/json')
                                        -> write('{"Message": "Registro excluído com sucesso"}');
                }elseif(is_array($resposta) && isset($resposta['idErro'])){
                        if($resposta['idErro'] == 5){
                            return $response    -> withStatus(200)
                            -> withHeader('Content-type', 'application/json')
                            -> write('{"Message": "Registro excluído com sucesso, porém houve um problema na exclusão da imagem"}');
                        }else{
                            $dadosJSON = createJSON($resposta);
                                return $response    -> withStatus(404)
                                                    -> withHeader('Content-type', 'application/json')
                                                    -> write('{"Message": "Houve um erro no processo de excluir",
                                                    "Erro":'.$dadosJSON.'}');
                        }
                }

            }else{
                return $response    -> withStatus(404)
                                    -> withHeader('Content-type', 'application/json')
                                    -> write('{"Message": "O id informado não existe na base de dados"}');
            }
        }else{
            return $response    -> withStatus(404)
                                -> withHeader('Content-type', 'application/json')
                                -> write('{"Message": "É obrigatório informar um id válido e númerico"}');
        }
  

        echo($id);
    });

    //executa todos os endpoints
    $app->run();
?>