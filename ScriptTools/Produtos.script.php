<?php
namespace TTScripts;

use Console\Console;
use Console\Out;
use Console\Color;

/**
 * Produtos
 * 
 * @author Fernando H Corrêa fernandohcorrea(TO)gmail.com
 * @version 1.0
 * @license http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 */
class Produtos extends \Console\Script\Base implements \Console\Script\Schema
{
    private $fb;
    private $filePath;
    private $fileSize;
    private $content;
    private $baseTags;
    private $listProducts;

    /**
     * Pode ser usado para inicar algumas variáveis
     * 
     * @see \Console\Script\Base::__construct()
     */
    public function __construct() {
        parent::__construct();
        $this->baseTags = $this->loadBaseTags();
    }

    /**
     * Inicia o Script
     * 
     * @param string $file Caminho do arquivo json de produtos
     * @return int
     */
    public function startScriptTool($file)
    {
        $this->filePath = $file;

        $this->readFile();
        $this->parseContent();
        $this->findProduct();

        return 0;
    }

    /**
     * Lê o arquivo passado para o script como argumento
     */
    private function readFile()
    {
        if(!is_file($this->filePath) || !is_readable($this->filePath)){
            throw new \Exception('Arquivo não encotrado ou não tem permissão de leitura');
        }

        $this->fb = fopen($this->filePath, 'r');
        $this->fileSize = filesize($this->filePath);
        if($this->fileSize == 0){
            throw new \Exception('Arquivo Vazio');
        }

        $this->content = fread($this->fb, $this->fileSize);

        fclose($this->fb);

        if(!$this->content){
            throw new \Exception('Arquivo sem conteúdo');
        }
    }
    
    /**
     * Parse do conteúdo
     */
    private function parseContent()
    {
        $data = json_decode($this->content, true);
        $listProducts = [];

        foreach($data['products'] as $product){
            $product['tagsVector'] = array_fill(0, count($this->baseTags), 0);
            $this->parseTags($product);
            $listProducts[$product['id']] = $product;
        }

        $this->listProducts = $listProducts;
    }

    /**
     * Parse das Tags de cada produto
     * @param &$product Produto por Referência
     */
    private function parseTags(&$product)
    {
        if(!isset($product['tags']) || count($product['tags']) == 0){
            return $product;
        }

        foreach($product['tags'] as $tag){
            $key = array_search(strtolower($tag), $this->baseTags);

            if($key === false){
                continue;
            }

            $product['tagsVector'][$key] = 1;
        }
    }

    /**
     * Inicia Processo de busca de Produto
     */
    private function findProduct()
    {
        $exit = false;
        do {
            $resp = $this->pergunta('Digite o ID do produto ou [S] para sair:');
            $this->limpaTela();
            if(strtoupper($resp) == 'S'){
                $exit = true;
            }

            if(is_numeric($resp)){
                $this->findByID($resp);
            }

        } while ($exit == false);

    }

    /**
     * Busca produto por ID e inicia pesquisa por similares
     */
    private function findByID($id)
    {
        if(!isset($this->listProducts[$id])){
            Out::sysOutNl('Produto não encontrado', Color::FG_LIGHT_RED);
            return;
        }

        $product = $this->listProducts[$id];
        $listSimilar = $this->findSimilar($product);

        Out::sysOut('ID: ', Color::FG_WHITE);
        Out::sysOutNl($product['id']);
        Out::sysOut('Produto: ', Color::FG_WHITE);
        Out::sysOutNl($product['name']);
        Out::sysOut('Tags: ', Color::FG_WHITE);
        Out::sysOutNl(implode(', ',$product['tags']));

        if($listSimilar){
            Out::sysOutNl('Similares: ', Color::FG_WHITE);
            foreach($listSimilar as $prod){
                Out::sysOutNl(sprintf('%s - %s com S = %s',
                    str_pad($prod['id'], 6, ' ', STR_PAD_LEFT),
                    str_pad($prod['name'], 40, ' ', STR_PAD_RIGHT),
                    $prod['similar']
                ));
            }
        }
        
        Out::sysOutNl('');

    }

    /**
     * Calcula e retorna similares
     */
    private function findSimilar($product, $qtd =3)
    {
        $pTagsVector = $product['tagsVector'];

        $similar = [];

        foreach($this->listProducts as $id => $dataProduct){
            if($id == $product['id']){
                continue;
            }

            $tagsVector = $dataProduct['tagsVector'];

            $sum = [];
            foreach($pTagsVector as $key => $val){
                $sum[] = pow(($pTagsVector[$key] - $tagsVector[$key]),2);
            }

            $D = sqrt(array_sum($sum));

            $S = 1/(1+$D);

            $similar[$id] = $S;
        }
        
        arsort($similar);

        $ret = [];
        foreach($similar as $id => $fator){
            if(count($ret) == $qtd){
                break;
            }

            $prod = $this->listProducts[$id];
            $prod['similar'] = $fator;
            $ret[] = $prod;
        }

        return $ret;
    }

    /**
     * Carregar Tags
     */
    private function loadBaseTags()
    {
        return [
            "neutro",
            "veludo",
            "couro",
            "basics",
            "festa",
            "workwear",
            "inverno",
            "boho",
            "estampas",
            "balada",
            "colorido",
            "casual",
            "liso",
            "moderno",
            "passeio",
            "metal",
            "viagem",
            "delicado",
            "descolado",
            "elastano"
        ];
    }
}