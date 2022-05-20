<?php

/******************************************************************************
 * Objetivo: Arquivo de rota, para segmentar as ações encaminhadas pela View
 *      (dados de um form, listagem de dados, ação de excluir ou atualizar).
 *      Este Arquivo será responsável por encaminhar as solicitações para a
 *      Controller.
 * Autor: Jennifer
 * Data: 04/03/2022
 * Versão: 1.0
 ******************************************************************************/
require_once('modulo/config.php');
$action = (string) null;
$component = (string) null;

// Validação para verificar se a requisição é um POST de um formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

  //Recebendo dados via URL para saber quem está solicitando e qual ação será realizada
  $component = strtoupper($_GET['component']);
  $action = strtoupper($_GET['action']);


  // Estrutura condicional para validar quem esta solicitando algo para o Router

  switch ($component) {
    case 'CONTATOS':
      // Import da controller Contatos
      require_once('controller/controllerContatos.php');

      // Validação para identificar o tipo de ação que será realizada
      if ($action == 'INSERIR') {

        if(isset($_FILES) && !empty($_FILES)){
          // Chama a função de inserir na controller
          $resposta = inserirContato($_POST, $_FILES);
        } else {
          $resposta = inserirContato($_POST, null);
        }

        // Valida se o retorno foi verdadeiro
        if (is_bool($resposta)) { // Se for booleano
          if ($resposta) {
            echo ("<script> 
                        alert('Registro inserido com sucesso!');
                        window.location.href = 'index.php';
                      </script>");
          }
          // Se o retorno for um array significa que houve erro no processo de inserção
        } elseif (is_array($resposta)) {
          echo ("<script>
                    alert('" . $resposta['message'] . "');
                    window.history.back();
                  </script>");
        }
      } elseif ($action == 'DELETAR') {

        // Recebe o id do registro que deverá ser excluido, que foi enviado pela url no link da imagem do excluir
        // que foi acionado na index
        $idContato = $_GET['id'];
        $foto = $_GET['foto'];

        // Criamos um array para encaminhar os valores do id e da foto
        $arrayDados = array(
          "id"    => $idContato,
          "foto"  => $foto
        );

        // Chama a função de excluir na controller
        $resposta = excluirContato($arrayDados);

        if (is_bool($resposta)) {

          if ($resposta) {
            echo ("<script> 
                        alert('Registro apagado com sucesso!');
                        window.location.href = 'index.php';
                      </script>");
          }
        } elseif (is_array($resposta)) {
          echo ("<script>
                    alert('" . $resposta['message'] . "');
                    window.history.back();
                  </script>");
        }
      } elseif ($action == 'BUSCAR') {

        // Recebe o id do registro que deverá ser editado, que foi enviado pela url no link da imagem do editar
        // que foi acionado na index
        $idContato = $_GET['id'];

        // Chama a função de buscar na controller
        $dados = buscarContato($idContato);

        // Ativa a utilização de variáveis de sessão no servidor
        // Sessão: Variáveis que permanecem no navegador mesmo acessando outros links do site
        session_start();

        // Guarda em uma variável de sessão os dados que o banco de dados retornou para a busca do id
        // Obs: essa variável de sessão será utilizada na index.php, para colocar os dados nas caixas de texto
        $_SESSION['dadosContato'] = $dados;

        // Utilizando o header também poderemos chamar a index.php, porém haverá uma ação de carregamento no navegador (piscando a tela novamente)
        // header('location: index.php');

        // importando a index, assim não havendo um novo carregamento da página  
        require_once('index.php');
      } elseif ($action == 'EDITAR') {

        // Recebe o id que foi encaminhado no action do form pela URL 
        $idContato = $_GET['id'];
        // Recebe o nome da foto que foi enviada pelo GET do form
        $foto = $_GET['foto'];

        // Cria um array contendo o id e o nome da foto para enviar a controller
        $arrayDados = array(
          "id"    => $idContato,
          "foto"  => $foto,
          "file"  => $_FILES
        );

        // Chama a função de editar na controller
        $resposta = atualizarContato($_POST, $arrayDados);
        // Valida se o retorno foi verdadeiro
        if (is_bool($resposta)) { // Se for booleano
          if ($resposta) {
            echo ("<script> 
                      alert('Registro atualizado com sucesso!');
                      window.location.href = 'index.php';
                    </script>");
          }
          // Se o retorno for um array significa que houve erro no processo de inserção
        } elseif (is_array($resposta)) {
          echo ("<script>
                  alert('" . $resposta['message'] . "');
                  window.history.back();
                </script>");
        }
      }
      break;
  }
}
