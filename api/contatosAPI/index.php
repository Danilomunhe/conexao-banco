<?php

    //request - recebe dados do corpo da requisição (JSON, FORM/DATA, XML)
    //response - envia dados de retorno da api
    //$args - Permite receber dados de atributos na API
    // Import do arquivo autoload, que fará as instancias do slim
    require_once('vendor/autoload.php');

    $app = new \Slim\App();

    // EndPoint: requisição para listar todos os contatos
    $app->get('/contatos', function($request, $response, $args){
        $response->write('Testando a API pelo GET');

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

    // EndPoint: requisição para inserir um novo contato
    $app->post('/contatos', function($request, $response, $args){


    });

    //executa todos os endpoints
    $app->run();
?>