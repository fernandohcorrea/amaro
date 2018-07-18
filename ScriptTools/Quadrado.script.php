<?php
namespace TTScripts;

/**
 * Calcula quadrado perfeito
 * 
 * @author Fernando H Corrêa fernandohcorrea(TO)gmail.com
 * @version 1.0
 * @license http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 */
class Quadrado extends \Console\Script\Base implements \Console\Script\Schema
{
    private $fb;
    private $filePath;
    private $fileSize;
    private $content;
    private $sum;    

    /**
     * Pode ser usado para inicar algumas variáveis
     * 
     * @see \Console\Script\Base::__construct()
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Inicia o Script
     * 
     * @param string $file Caminho do arquivo
     * @return int
     */
    public function startScriptTool($file)
    {
        $this->filePath = $file;

        $this->readFile();
        $this->parseContent();
        $this->showResult();

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
     * Valida o conteúdo do arquivo somando linhas, colunas, diagonais para o quadrado perfeito
     */
    private function parseContent()
    {
        $data = [];
        $lines= explode(PHP_EOL, $this->content);
        $countItems = 0;
        $lastSumLine = 0;

        $sumDDown = [];
        $sumDUp = [];
        $sumCol=[];

        foreach($lines as $idxL => $line){
            $data[$idxL] = explode(' ', $line);

            $this->sum[$idxL] = array_sum($data[$idxL]);

            if($idxL == 0){
                $lastCountItems = count($data[$idxL]);
                $countItems = count($data[$idxL]);
                $lastSumLine = $this->sum[$idxL];
            }

            if($lastCountItems != count($data[$idxL]) || ( $lastSumLine != $this->sum[$idxL] ) ){
                throw new \Exception('Não é uma quadrado perfeito');
            }

            $lastCountItems = count($data[$idxL]);
            $lastSumLine = $this->sum[$idxL];

            foreach($data[$idxL] as $idxC => $val){

                if($idxL == $idxC){
                    $sumDDown[] = $val;
                }

                if( ($idxL + $idxC) == ($countItems -1) ){
                    $sumDUp[] = $val;
                }
 
                if(isset($sumCol[$idxC])){
                    $sumCol[$idxC] += $val;
                } else {
                    $sumCol[$idxC] = $val;
                }
                    
            }

        }

        if($lastCountItems != ($idxL+1)){
            throw new \Exception('Não é uma quadrado perfeito');
        }

        $sumDDown = array_sum($sumDDown);
        $sumDUp = array_sum($sumDUp);
        $this->sum = array_merge($this->sum, $sumCol, [$sumDDown, $sumDUp]);
    }

    /**
     * Resultado
     */
    private function showResult()
    {
        if(count(array_unique($this->sum)) == 1){
            \Console\Out::sysOutNl("O quadrado é perfeito!", \Console\Color::FG_LIGHT_GREEN);
        } else {
            throw new \Exception('Não é uma quadrado perfeito');
        }
    }

}