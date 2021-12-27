<?php

namespace Eduardokum\LaravelBoleto\Boleto\Banco;

use Eduardokum\LaravelBoleto\Boleto\AbstractBoleto;
use Eduardokum\LaravelBoleto\CalculoDV;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Util;
use Exception;

class Inter extends AbstractBoleto implements BoletoContract
{
    const MULTA_NAO_TEM = 0;
    const MULTA_VALOR_FIXO = 1;
    const MULTA_PERCENTUAL = 2;

    const MORA_DIA_NAO_TEM = 0;
    const MORA_DIA_VALOR_FIXO = 1;
    const MORA_DIA_PERCENTUAL = 2;

    const DESCONTO_NAO_TEM = 0;
    const DESCONTO_FIXO_ATE_DATA_INFORMADA = 1;
    const DESCONTO_VALOR_POR_ANTECIPACAO_DIA_CORRIDO = 2;
    const DESCONTO_VALOR_POR_ANTECIPACAO_DIA_UTIL = 3;
    const DESCONTO_PERCENTUAL_ATE_DATA_INFORMADA = 4;
    const DESCONTO_PERCENTUAL_POR_ANTECIPACAO_DIA_CORRIDO = 5;
    const DESCONTO_PERCENTUAL_POR_ANTECIPACAO_DIA_UTIL = 6;

    /**
     * Local de pagamento
     *
     * @var string
     */
    protected $localPagamento = 'PAGÁVEL EM QUALQUER BANCO';

    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = self::COD_BANCO_INTER;

    /**
     * Maximo de dias corridos para receber apos o vencimento
     *
     * @var int
     */
    protected $maxDiasVencidos = 30;

    /**
     * Modo multa
     *
     * @var int
     */
    protected $modoMulta = 0;

    /**
     * Modo juros/mora
     *
     * @var int
     */
    protected $modoMoraDia = 0;

    /**
     * Modo desconto
     *
     * @var int
     */
    protected $modoDesconto = 0;

    /**
     * valor abatimento
     *
     * @var float
     */
    protected $valorAbatimento = 0.00;

    /**
     * Variáveis adicionais.
     *
     * @var array
     */
    public $variaveis_adicionais = [
        'carteira_nome' => '',
    ];

    /**
     * max dias para data limite de pagamento
     *
     * @var array
     */
    protected $optionMaxDiasVencidos = [
        30, 60
    ];

    /**
     * Define as carteiras disponíveis para este banco
     *
     * @var array
     */
    protected $carteiras = ['112'];
    /**
     * Espécie do documento, coódigo para remessa
     *
     * @var string
     */
    protected $especiesCodigo240 = [
        'CH' => '01',
        'DM' => '02',
        'DMI' => '03',
        'DS' => '04',
        'DSI' => '05',
        'DR' => '06',
        'LC' => '07',
        'NCC' => '08',
        'NCE' => '09',
        'NCI' => '10',
        'NCR' => '11',
        'NP' => '12',
        'NPR' => '13',
        'TM' => '14',
        'TS' => '15',
        'NS' => '16',
        'RC' => '17',
        'FAT' => '18',
        'ND' => '19',
        'AP' => '20',
        'ME' => '21',
        'PC' => '22',
        'NF' => '23',
        'DD' => '24',
        'CPR' => '25',
        'OS' => '99', // Outros 
    ];

    /**
     * Espécie do documento, coódigo para remessa
     *
     * @var string
     */
    protected $especiesCodigo400 = [
        'DM' => '01',
        'NP' => '02',
        'NS' => '03',
        'CS' => '04',
        'REC' => '05',
        'LC' => '10',
        'ND' => '11',
        'DS' => '12',
        'OS' => '99',
    ];

    public function setMaxDiasVencidos($maxDiasVencidos)
    {
        if (!in_array($maxDiasVencidos, $this->optionMaxDiasVencidos)) {
            throw new Exception("Valor inválido para a informação da qtda de dias corridos para pagamento");
        }
        $this->maxDiasVencidos = $maxDiasVencidos;
    }

    public function getMaxDiasVencidos()
    {
        return $this->maxDiasVencidos;
    }

    public function setModoMulta($modoMulta)
    {
        $this->modoMulta = $modoMulta;
    }

    public function getModoMulta()
    {
        return $this->modoMulta;
    }

    public function setModoMoraDia($modoMoraDia)
    {
        $this->modoMoraDia = $modoMoraDia;
    }

    public function getModoMoraDia()
    {
        return $this->modoMoraDia;
    }

    public function setModoDesconto($modoDesconto)
    {
        $this->modoDesconto = $modoDesconto;
    }

    public function getModoDesconto()
    {
        return $this->modoDesconto;
    }

    public function setValorAbatimento($valorAbatimento)
    {
        $this->valorAbatimento = $valorAbatimento;
    }

    public function getValorAbatimento()
    {
        return $this->valorAbatimento;
    }

    /**
     * Seta dias para baixa automática
     *
     * @param int $baixaAutomatica
     *
     * @return $this
     * @throws \Exception
     */
    public function setDiasBaixaAutomatica($baixaAutomatica)
    {
        if ($this->getDiasProtesto() > 0) {
            throw new \Exception('Você deve usar dias de protesto ou dias de baixa, nunca os 2');
        }
        $baixaAutomatica = (int) $baixaAutomatica;
        $this->diasBaixaAutomatica = $baixaAutomatica > 0 ? $baixaAutomatica : 0;
        return $this;
    }

    /**
     * Gera o Nosso Número.
     *
     * @return string
     * @throws \Exception
     */
    protected function gerarNossoNumero()
    {
        $numero_boleto = Util::numberFormatGeral($this->getNumero(), 8);
        $carteira = Util::numberFormatGeral($this->getCarteira(), 3);
        $dv = CalculoDV::interNossoNumero($carteira, $numero_boleto);
        return $numero_boleto . $dv;
        //return Util::numberFormatGeral($this->getNumero(), 11);
    }

    /**
     * Retorna o campo Agência/Beneficiário do boleto
     *
     * @return string
     */
    public function getAgenciaCodigoBeneficiario()
    {
        $agencia = Util::numberFormatGeral($this->getAgencia(), 4);
        $codigoCliente = Util::numberFormatGeral($this->getConta(), 9);

        return $agencia . ' / ' . $codigoCliente;
    }
    
    /**
     * Método que retorna o nosso numero usado no boleto. alguns bancos possuem algumas diferenças.
     *
     * @return string
     */
    public function getNossoNumeroBoleto()
    {
        return $this->getCarteira() . '/' . substr_replace($this->getNossoNumero(), '-', -1, 0);
    }
    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * @return string
     * @throws \Exception
     */
    protected function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }

        $campoLivre = Util::numberFormatGeral($this->getCarteira(), 3);
        $campoLivre .= Util::numberFormatGeral($this->getAgencia(), 4);
        $campoLivre .= Util::numberFormatGeral($this->getConta(), 7);
        $campoLivre .= Util::numberFormatGeral($this->getNossoNumero(), 11);

        return $this->campoLivre = $campoLivre;
    }

    /**
     * Método onde qualquer boleto deve extender para gerar o código da posição de 20 a 44
     *
     * @param $campoLivre
     *
     * @return array
     */
    public static function parseCampoLivre($campoLivre)
    {
        return [
            'convenio' => null,
            'agenciaDv' => null,
            'codigoCliente' => null,
            'carteira' => substr($campoLivre, 0, 3),
            'nossoNumero' => substr($campoLivre, 3, 8),
            'nossoNumeroDv' => substr($campoLivre, 11, 1),
            'nossoNumeroFull' => substr($campoLivre, 3, 9),
            'agencia' => substr($campoLivre, 12, 4),
            'contaCorrente' => substr($campoLivre, 16, 5),
            'contaCorrenteDv' => substr($campoLivre, 21, 1),
        ];
    }
}
