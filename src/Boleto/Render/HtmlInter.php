<?php
namespace Eduardokum\LaravelBoleto\Boleto\Render;

class HtmlInter extends Html
{    
    /**
     * Retorna a string contendo as imagens do código de barras, segundo o padrão Febraban
     *
     * @param $codigo_barras
     *
     * @return string
     */
    public function getImagemCodigoDeBarras($codigo_barras)
    {
        $codigo_barras = (strlen($codigo_barras)%2 != 0 ? '0' : '') . $codigo_barras;
        $barcodes = ['00110', '10001', '01001', '11000', '00101', '10100', '01100', '00011', '10010', '01010'];
        for ($f1 = 9; $f1 >= 0; $f1--) {
            for ($f2 = 9; $f2 >= 0; $f2--) {
                $f = ($f1*10) + $f2;
                $texto = "";
                for ($i = 1; $i < 6; $i++) {
                    $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
                }
                $barcodes[$f] = $texto;
            }
        }
        
        // Guarda inicial
        $retorno = '<div class="barcode">' .
            '<div class="black thin"></div>' .
            '<div class="white thin"></div>' .
            '<div class="black thin"></div>' .
            '<div class="white thin"></div>';

        // Draw dos dados
        while (strlen($codigo_barras) > 0) {
            $i = round(substr($codigo_barras, 0, 2));
            $codigo_barras = substr($codigo_barras, strlen($codigo_barras) - (strlen($codigo_barras) - 2), strlen($codigo_barras) - 2);
            $f = $barcodes[$i];
            for ($i = 1; $i < 11; $i += 2) {
                if (substr($f, ($i - 1), 1) == "0") {
                    $f1 = 'thin';
                } else {
                    $f1 = 'large';
                }
                $retorno .= "<div class='black {$f1}'></div>";
                if (substr($f, $i, 1) == "0") {
                    $f2 = 'thin';
                } else {
                    $f2 = 'large';
                }
                $retorno .= "<div class='white {$f2}'></div>";
            }
        }

        // Final
        return $retorno . '<div class="black large"></div>' .
        '<div class="white thin"></div>' .
        '<div class="black thin"></div>' .
        '</div>';
    }

    /**
     * função para gerar o boleto
     *
     * @return string
     * @throws \Exception
     */
    public function gerarBoleto()
    {
        if (count($this->boleto) == 0) {
            throw new \Exception('Nenhum Boleto adicionado');
        }

        return $this->getBlade()->make('BoletoHtmlRender::boleto-inter', [
            'boletos' => $this->boleto,
            'css' => $this->writeCss(),
            'imprimir_carregamento' => (bool) $this->print,
            'mostrar_instrucoes' => (bool) $this->showInstrucoes,
        ])->render();
    }

    /**
     * função para gerar o carne
     *
     * @return string
     * @throws \Exception
     */
    public function gerarCarne()
    {
        if (count($this->boleto) == 0) {
            throw new \Exception('Nenhum Boleto adicionado');
        }

        return $this->getBlade()->make('BoletoHtmlRender::carne-inter', [
            'boletos' => $this->boleto,
            'css' => $this->writeCss(),
            'imprimir_carregamento' => (bool) $this->print,
            'mostrar_instrucoes' => (bool) $this->showInstrucoes,
        ])->render();
    }
}
