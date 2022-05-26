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
    $app->post('/contatos', function($request, $response, $args){

          //import dos arquivos
          require_once("../modulo/config.php");
          require_once("../controller/controllerContatos.php");

        //recebe do  header a requisição qual será o content -type
        $contentTypeHeader = $request->getHeaderLine('Content-Type');

        //Cria um array, pois dependendo do content-type temos mais informações separadas por (;)
        $contentType = explode(";", $contentTypeHeader);

        switch ($contentType[0]){
            case 'multipart/form-data':

                //Recebe os dados comuns enviados pelo corpo da requisição
                $dadosBody = $request->getParsedBody();

                //Recebe uma imagem enviada pelo corpo da requisição
                $uploadFiles = $request->getUploadedFiles();

                //Cria um array com todos os dados que chegaram na requisição, devido aos dados serem protegidos
                //criamos um array e recuperamos os dados pelos métodos do objeto
                $arrayFoto = array(
                    "name" => $uploadFiles['foto']->getClientFileName(),
                    "type" => $uploadFiles['foto']->getClientMediaType(),
                    "size" => $uploadFiles['foto']->getSize(),
                    "tmp_name" => $uploadFiles['foto']->file
                );

                //Cria uma chave chamada "foto" para colocar todos os dados do objeto conforme é gerado em um form
                $file = array("foto" => $arrayFoto);

                //cria um array com todos os dados comuns e do arquivo que será enviado para o servidor
                $arrayDados = array( $dadosBody,
                                     "file" => $file

                );

                $resposta = inserirContato($arrayDados);
                
                
                if(is_bool($resposta) && $resposta == true){
                        return $response    -> withStatus(200)
                                            -> withHeader('Content-type', 'application/json')
                                            -> write('{"Message": "Registro inserido com sucesso"}');
                }elseif(is_array($resposta) && $resposta['idErro']){

                    $dadosJSON = createJSON($resposta);
                    return $response -> withStatus(200)
                                     -> withHeader('Content-type', 'application/json')
                                     -> write('{"Message": "Registro inserido com sucesso",
                                                "Erro": '.$dadosJSON.'}');
                }
               
                break;
            case 'application/json':

                return $response    -> withStatus(200)
                                    -> withHeader('Content-type', 'application/json')
                                    -> write('{"Message": "Formato selecionado foi JSON"}');
                break;

            default:
                return $response    -> withStatus(400)
                                    -> withHeader('Content-type', 'application/json')
                                    -> write('{"Message": "Formato do Content-Type não é válido par essa requisição"}');
        }

        
    });
    //executa todos os endpoints
    $app->run();
?>