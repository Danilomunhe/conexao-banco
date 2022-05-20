<?php
/*******************************************************
 *    Arquivo para criar a conexão com o banco de dados.
 * 
 *  Jennifer Cristina 25/02/2022
 *  Versão 1.0
 *****************************************************/

const SERVER = 'localhost';
const USER = 'root';
const PASSWORD = 'bcd127';
const DATABASE = 'dbContatos';

// $resultado = conectarMysql();

// echo('<pre>');
// print_r($resultado);
// echo('</pre>');

// abre a conexão com o Banco de dados Mysql
 function conectarMysql(){
    $conexao = array();

    // Retorna um array de dados sobre a conexão, caso a mesma dê certo, abre a porta do banco de dados
    $conexao = mysqli_connect(SERVER, USER, PASSWORD, DATABASE);

    // Validação para saber se a conexão foi realizada com sucesso
    if($conexao){
        return $conexao;
   
    }else{
        return false;
    }
 }

 // Fecha a conexão com o Banco de dados Mysql
 function fecharConexaoMysql($conexao){

    mysqli_close($conexao);
 }
 /*
    Existem três formas de realizar a conexão com o DB MySQL no PHP:

        mysql_connect() - versão antiga do PHP de fazer a conexão com o DB (não oferece performace e segurança)

        mysqli_connect() - versão mais atualizada no PHP de fazer a conexão com o DB (pode ser utilizada para Programação Estruturada e POO)

        PDO() - versão mais completa e eficiente para conexão com DB (é indicada pela segurança e para POO)
  */
?>