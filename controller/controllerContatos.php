<?php

/********************************************************************************************
 * Objetivo: Arquivo responsavel pela manipulação de dados de contatos, é aqui que fazemos todos os ajustes antes de mandar para o banco
 *  Obs(Este arquivo fará a ponte entre a View e a Model)
 * Autor: Jennifer
 * Data: 04/03/2022
 * Versão: 1.0
 *************************************************************************************/

require_once(SRC.'modulo/config.php');

// Função para receber dados da View w caminhar para a model (inserir)
function inserirContato($dadosContato, $file)
{

    $nomeFoto = (string) null;
    // Validação para verificar se o objeto esta vazio
    if (!empty($dadosContato)) {
        // Validação de caixa vazia dos elementos nome, celular e email, pois são obrigatórios no banco de dados
        if (!empty($dadosContato['txtNome']) && !empty($dadosContato['txtCelular']) && !empty($dadosContato['txtEmail']) && !empty($dadosContato['sltEstado'])) {

            // Validação para identificar se chegou um arquivo para upload
            if ($file['fileFoto']['name'] != null) {

                // Import da função de upload
                require_once('modulo/upload.php');
                // Chama a função de upload colocando-a em uma variável
                $nomeFoto = uploadFile($file['fileFoto']);

                if (is_array($nomeFoto)) {

                    // Caso aconteça algum erro no processo de upload, a função irá retornar um array com a possível mensagem de erro.
                    // Esse array será retornado para a router e ela irá exibir a mensagem para o usuário
                    return $nomeFoto;
                }
            }

            // Criação do array de dados que será encaminhado a model para inserir no banco de dados, é importante criar este array
            // conforme as necessidades de manipulação do banco de dados
            // Observação: Criar as chaves conforme os nomes dos atributos do banco de dados
            $arrayDados = array(
                "nome"      => $dadosContato['txtNome'],
                "telefone"  => $dadosContato['txtTelefone'],
                "celular"   => $dadosContato['txtCelular'],
                "email"     => $dadosContato['txtEmail'],
                "obs"       => $dadosContato['txtObs'],
                "foto"      => $nomeFoto,
                "idestado"  => $dadosContato['sltEstado']

            );

            // import do arquivo de modelagem para manipular o BD
            require_once('./model/bd/contato.php');
            // Chama a função que fará o insert no BD (esta função esta no model)
            if (insertContato($arrayDados))
                return true;
            else
                return array(
                    'idErro'  => 1,
                    'message' => 'Não foi possível inserir os dados no Banco de Dados'
                );
        } else
            return array(
                'idErro'  => 2,
                'message' => 'Existem campos obrigatórios que não foram inseridos'
            );
    }
}

// Função para receber dados da View w caminhar para a model (atualizar)
function atualizarContato($dadosContato, $arrayDados)
{
    $statusUpload = (bool) false;
    //Recebe o id enviado pelo arrayDados
    $idContato = $arrayDados['id'];

    //Recebe a foto enviada pelo arrayDados (nome da foto que já existe no bd)
    $foto = $arrayDados['foto'];

    //Recebe o arquivo 
    $file = $arrayDados['file'];

    // Validação para verificar se o objeto esta vazio
    if (!empty($dadosContato)) {
        // Validação de caixa vazia dos elementos nome, celular e email, pois são obrigatórios no banco de dados
        if (!empty($dadosContato['txtNome']) && !empty($dadosContato['txtCelular']) && !empty($dadosContato['txtEmail'])) {

            // Validação para garantir que id seja válido
            if (!empty($idContato) && $idContato != 0 && is_numeric($idContato)) {

                // Validação para identificar se será enviado ao servidor uma nova foto
                if ($file['fileFoto']['name'] != null) {

                    // Import da função de upload
                    require_once('modulo/upload.php');

                    // Chama a função de upload colocando-a em uma variável para enviar a nova foto ao servidor
                    $novaFoto = uploadFile($file['fileFoto']);
                    $statusUpload = true;
                } else {
                    // Permanece a mesma foto no bd
                    $novaFoto = $foto;
                }

                // Criação do array de dados que será encaminhado a model para inserir no banco de dados, é importante criar este array
                // conforme as necessidades de manipulação do banco de dados
                // Observação: Criar as chaves conforme os nomes dos atributos do banco de dados
                $arrayDados = array(
                    "id"        => $idContato,
                    "nome"      => $dadosContato['txtNome'],
                    "telefone"  => $dadosContato['txtTelefone'],
                    "celular"   => $dadosContato['txtCelular'],
                    "email"     => $dadosContato['txtEmail'],
                    "obs"       => $dadosContato['txtObs'],
                    "foto"      => $novaFoto,
                    "idEstado"  => $dadosContato['sltEstado']
                );

                // import do arquivo de modelagem para manipular o BD
                require_once('./model/bd/contato.php');
                // Chama a função que fará o insert no BD (esta função esta no model)
                if (updateContato($arrayDados)) {

                    // Validação para verificar se será necessário apagar a foto antiga 
                    // Esta variável foi ativada em true quando realizar o upload de uma nova foto no servidor (linha 90)
                    if ($statusUpload) {
                        // Apaga a foto antiga da pasta do servidor 
                        unlink(DIRETORIO_FILE_UPLOAD . $foto);
                    }
                    return true;
                } else
                    return array(
                        'idErro'  => 1,
                        'message' => 'Não foi possível atualizar os dados no Banco de Dados'
                    );
            } else
                return array(
                    'idErro'  => 4,
                    'message' => 'Não é possível editar um registro sem informar um id válido'
                );
        } else
            return array(
                'idErro'  => 2,
                'message' => 'Existem campos obrigatórios que não foram inseridos'
            );
    }
}

// Função para realizar a exclusão de um contato 
function excluirContato($arrayDados)
{
    // Recebe o id do registro que será excluido 
    $id = $arrayDados['id'];
    // Recebe o nome da foto que será excluída da pasta do servidor
    $foto = $arrayDados['foto'];

    // Validação para verificar se id contém um numero válido
    if ($id != 0 && !empty($id) && is_numeric($id)) {

        // import do arquivo de contato
        require_once(SRC.'model/bd/contato.php');
        // import do arquivo de configurações do projeto
        // require_once('modulo/config.php');

        // Chama a função da model e valida se o retorno foi verdadeiro ou false
        if (deleteContato($id)) {

            if ($foto != null) {

                // Permite apagar a foto fisicamente do diretório no servidor (unlink())
                @unlink(SRC.DIRETORIO_FILE_UPLOAD . $foto);
                return true;
            } else
                return true;
        } else
            return array(
                'idErro'  => 3,
                'message' => 'O banco não pode excluir o registro'
            );
    } else
        return array(
            'idErro'  => 4,
            'message' => 'Não é possível excluir um registro sem informar um id válido'
        );
}

// Função para solicitar os dados da model e encaminhar a lista de contatos para a View
function listarContato()
{

    // import do arquivo que vai buscar os dados no Banco de dados
    require_once(SRC.'./model/bd/contato.php');

    // Chama a função que vai buscar os dados no Banco de dados
    $dados = selectAllContato();

    if (!empty($dados))
        return $dados;
    else
        return false;
}

// Função para buscar um contato através do id do registro
function buscarContato($id)
{

    // Validação para verificar se id contém um numero válido
    if ($id != 0 && !empty($id) && is_numeric($id)) {

        // import do arquivo de contato
        require_once(SRC.'model/bd/contato.php');

        // Chama a função na model que vai buscar no banco de dados
        $dados = selectByIdContato($id);

        // Valida se existem dados para serem devolvidos
        if (!empty($dados))
            return $dados;
        else
            return false;
    } else {
        return array(
            'idErro'  => 4,
            'message' => 'Não é possível buscar um registro sem informar um id válido'
        );
    }
}
