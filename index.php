<?php
// import do arquivo de configurações do projeto
require_once('modulo/config.php');

// @ - Esconder o erro debaixo do tabela, não fazer isso!

// $nome = (String) null;
// $telefone = (String) null;
// $celular = (String) null;
// $email = (String) null;
// $obs = (String) null;

// Essa variavel foi criada para diferenciar no action do formulário, qual ação deveria ser levada para o router (inserir ou editar).
// Nas condições abaixo, mudamos o action dessa variavel para a ação de editar
$form = (string) "router.php?component=contatos&action=inserir";
// Variável para carregar o nome da foto do banco de dados
$foto = (string) null;
// Variável para ser utlizada no carregar dos estados (opção de editar)
$idEstado = (string) null;

// Valida se a utilização de variáveis de sessão esta ativa no servidor
if (session_status()) {
    // Valida se a variável de sessão dadosContato não esta vázia
    if (!empty($_SESSION['dadosContato'])) {
        $id        = $_SESSION['dadosContato']['id'];
        $nome      = $_SESSION['dadosContato']['nome'];
        $telefone  = $_SESSION['dadosContato']['telefone'];
        $celular   = $_SESSION['dadosContato']['celular'];
        $email     = $_SESSION['dadosContato']['email'];
        $obs       = $_SESSION['dadosContato']['obs'];
        $foto      = $_SESSION['dadosContato']['foto'];
        $idEstado  = $_SESSION['dadosContato']['idEstado'];

        // Mudamos a ação do form para editar o registro no click do botao salvar
        $form = (string) "router.php?component=contatos&action=editar&id=" . $id . "&foto=" . $foto;

        //Destroi uma variável da memória do servidor 
        unset($_SESSION['dadosContato']);
    }
}

?>
<!DOCTYPE>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title> Cadastro </title>
    <link rel="stylesheet" type="text/css" href="css/style.css">

</head>

<body>

    <div id="cadastro">
        <div id="cadastroTitulo">
            <h1> Cadastro de Contatos </h1>

        </div>
        <div id="cadastroInformacoes">
            <!-- enctype="multipart/form-data"
            Essa opção é obrigatória para enviar arquivos do formulário em html para o servidor. -->
            <form action="<?= $form ?>" name="frmCadastro" method="post" enctype="multipart/form-data">
                <div class="campos">
                    <div class="cadastroInformacoesPessoais">
                        <label> Nome: </label>
                    </div>
                    <div class="cadastroEntradaDeDados">
                        <input type="text" name="nome" value="<?= isset($nome) ? $nome : null ?>" placeholder="Digite seu Nome" maxlength="100">
                    </div>
                </div>

                <div class="campos">
                    <div class="cadastroInformacoesPessoais">
                        <label> Estado: </label>
                    </div>
                    <div class="cadastroEntradaDeDados">
                        <select name="estado">
                            <option value=""> Selecione um item </option>
                            <?php

                            // import da controller de estados
                            require_once('controller/controllerEstados.php');

                            // Chama a função para carregar todos os estados do banco 
                            $listEstados = listarEstado();

                            foreach ($listEstados as $item) {
                            ?>
                                <option <?= $idEstado == $item['idEstado'] ? 'selected' : null ?> value="<?= $item['idEstado'] ?>"><?= $item['nome'] ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="campos">
                    <div class="cadastroInformacoesPessoais">
                        <label> Telefone: </label>
                    </div>
                    <div class="cadastroEntradaDeDados">
                        <input type="tel" name="telefone" value="<?= isset($telefone) ? $telefone : null ?>">
                    </div>
                </div>
                <div class="campos">
                    <div class="cadastroInformacoesPessoais">
                        <label> Celular: </label>
                    </div>
                    <div class="cadastroEntradaDeDados">
                        <input type="tel" name="celular" value="<?= isset($celular) ? $celular : null ?>">
                    </div>
                </div>


                <div class="campos">
                    <div class="cadastroInformacoesPessoais">
                        <label> Email: </label>
                    </div>
                    <div class="cadastroEntradaDeDados">
                        <input type="email" name="email" value="<?= isset($email) ? $email : null ?>">
                    </div>
                </div>
                <div class="campos">
                    <div class="cadastroInformacoesPessoais">
                        <label> Escolha um arquivo: </label>
                    </div>
                    <div class="cadastroEntradaDeDados">
                        <input type="file" name="foto" accept=".jpg, .png, .webp, .jpeg, .gif">
                    </div>
                </div>
                <div class="campos">
                    <div class="cadastroInformacoesPessoais">
                        <label> Observações: </label>
                    </div>
                    <div class="cadastroEntradaDeDados">
                        <textarea name="obs" cols="50" rows="7"><?= isset($obs) ? $email : null ?></textarea>
                    </div>
                </div>
                <div class="campos">
                    <img src="<?= DIRETORIO_FILE_UPLOAD . $foto ?>">
                </div>
                <div class="enviar">
                    <div class="enviar">
                        <input type="submit" name="btnEnviar" value="Salvar">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="consultaDeDados">
        <table id="tblConsulta">
            <tr>
                <td id="tblTitulo" colspan="6">
                    <h1> Consulta de Dados.</h1>
                </td>
            </tr>
            <tr id="tblLinhas">
                <td class="tblColunas destaque"> Nome </td>
                <td class="tblColunas destaque"> Celular </td>
                <td class="tblColunas destaque"> Email </td>
                <td class="tblColunas destaque"> Foto </td>
                <td class="tblColunas destaque"> Opções </td>
            </tr>

            <?php
            // import do arquivo da controller para solicitar a listagem dos dados:
            require_once('controller/controllerContatos.php');

            // Chama a função que vai retornar os dados de contatos
            if ($listContato = listarContato()) {

                // Estrutura de repetição para retornar os dados dos array e printar na tela
                foreach ($listContato as $item) {

                    // variavel para carregar a foto que veio do banco de dados
                    $foto = $item['foto'];

            ?>

                    <tr id="tblLinhas">
                        <td class="tblColunas registros"><?= $item['nome'] ?></td>
                        <td class="tblColunas registros"><?= $item['celular'] ?></td>
                        <td class="tblColunas registros"><?= $item['email'] ?></td>
                        <td class="tblColunas registros"><img src="<?= DIRETORIO_FILE_UPLOAD . $foto ?>" class="foto"></td>

                        <td class="tblColunas registros">

                            <a href="router.php?component=contatos&action=buscar&id=<?= $item['id'] ?>">
                                <img src="img/edit.png" alt="Editar" title="Editar" class="editar">
                            </a>

                            <a onclick="return confirm('Deseja realmente excluir esse registro?')" href="router.php?component=contatos&action=deletar&id=<?= $item['id'] ?>&foto=<?= $foto ?>">
                                <img src="img/trash.png" alt="Excluir" title="Excluir" class="excluir">
                            </a>

                            <img src="img/search.png" alt="Visualizar" title="Visualizar" class="pesquisar">
                        </td>
                    </tr>

            <?php
                }
            }
            ?>
        </table>
    </div>
</body>

</html>