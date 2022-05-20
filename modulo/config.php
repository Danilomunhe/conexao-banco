<?php
/********************************************************************************************
 * Objetivo: Arquivo responsável pela criação de variáveis e constantes do projeto
 * Autor: Jennifer
 * Data: 25/04/2022
 * Versão: 1.0
 *************************************************************************************/

// Limitação de 5 Megabytes para uploads de imagens
const MAX_FILE_UPLOAD = 5120;

// Limitação de extensões de imagens
const EXT_FILE_UPLOAD = array("image/jpg", "image/png", "image/gif", "image/jpeg", "image/webp");

const DIRETORIO_FILE_UPLOAD = "arquivos/";

define( 'SRC',  $_SERVER['DOCUMENT_ROOT'].'/Danilo/conexaoBanco/');

    function createJSON ($arrayDados){
        //valiação para tratar array sem dados
        if(!empty($arrayDados)){

            //configura o padrão da conversão para o formato JSON
            header('Content-Type: application/json');
            $dadosJson = json_encode($arrayDados);
    
            //json_encode() converte um array para json
            //json_decode() converte um json para array
    
            return $dadosJson;
        }else{
            return false;
        }
      
    }

?>