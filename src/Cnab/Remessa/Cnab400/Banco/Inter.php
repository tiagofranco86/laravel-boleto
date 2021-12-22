<?php

namespace Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco;

use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\AbstractRemessa;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Contracts\Cnab\Remessa as RemessaContract;
use Eduardokum\LaravelBoleto\Util;

class Inter extends AbstractRemessa implements RemessaContract
{       
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_INTER;

    /**
     * Define as carteiras disponíveis para cada banco
     *
     * @var array
     */
    protected $carteiras = ['112'];

    /**
     * Caracter de fim de linha
     *
     * @var string
     */
    protected $fimLinha = "\r\n";

    /**
     * Caracter de fim de arquivo
     *
     * @var null
     */
    protected $fimArquivo = "\r\n";

    /**
     * @return $this
     * @throws \Exception
     */
    protected function header()
    {
        $this->iniciaHeader();

        $this->add(1, 1, '0');
        $this->add(2, 2, '1');
        $this->add(3, 9, 'REMESSA');
        $this->add(10, 11, '01');
        $this->add(12, 26, Util::formatCnab('X', 'COBRANCA', 15));
        $this->add(27, 46, '');
        $this->add(47, 76, Util::formatCnab('X', $this->getBeneficiario()->getNome(), 30));
        $this->add(77, 79, $this->getCodigoBanco());
        $this->add(80, 94, Util::formatCnab('X', 'Inter', 15));
        $this->add(95, 100, $this->getDataRemessa('dmy'));
        $this->add(101, 110, '');
        $this->add(111, 117, Util::formatCnab('9', $this->getIdremessa(), 7));
        $this->add(118, 394, '');
        $this->add(395, 400, Util::formatCnab('9', 1, 6));

        return $this;
    }

    /**
     * @param BoletoContract $boleto
     *
     * @return $this
     * @throws \Exception
     */
    public function addBoleto(BoletoContract $boleto)
    {
        $this->boletos[] = $boleto;
        $this->iniciaDetalhe();
        $this->add(1, 1, '1');
        $this->add(2, 20, '');
        $this->add(21, 37, Util::formatCnab('9', $this->getCarteiraNumero(), 3) .
            Util::formatCnab('9', $this->getAgencia(), 4) . Util::formatCnab('9', $this->getConta(), 10));
        $this->add(38, 62, Util::formatCnab('X', $boleto->getNumeroControle(), 25)); // numero de controle
        $this->add(63, 65, '');
        $this->add(66, 66, $boleto->getModoMulta());
        $this->add(67, 79, Util::formatCnab('9', $boleto->getModoMulta() == 1 ? $boleto->getMulta() : 0, 13, 2));
        $this->add(80, 83, Util::formatCnab('9', $boleto->getModoMulta() == 2 ? $boleto->getMulta() : 0, 4, 2));
        $this->add(84, 89, $boleto->getJurosApos() === false ? '000000' : $boleto->getDataVencimento()->copy()->addDays($boleto->getJurosApos())->format('dmy'));        
        $this->add(90, 100, Util::formatCnab('X', $boleto->getNossoNumero(), 11));
        $this->add(101, 108, Util::formatCnab('X', '', 8));
        $this->add(109, 110, "01");
        $this->add(111, 120, Util::formatCnab('9', $boleto->getNumeroDocumento(), 10));
        $this->add(121, 126, $boleto->getDataVencimento()->format('dmy'));
        $this->add(127, 139, Util::formatCnab('9', $boleto->getValor(), 13));
        $this->add(140, 141, $boleto->getMaxDiasVencidos());
        $this->add(142, 147, '000000');
        $this->add(148, 149, $boleto->getEspecieDocCodigo());
        $this->add(150, 150, "N");
        $this->add(151, 156, $boleto->getDataDocumento()->format('dmy'));
        $this->add(157, 159, "");
        $this->add(160, 160, $boleto->getModoMoraDia());
        $this->add(161, 173, Util::formatCnab('9', $boleto->getModoMoraDia() == 1 ? "0" : $boleto->getMoraDia(), 13, 2));
        $this->add(174, 177, Util::formatCnab('9', $boleto->getModoMoraDia() == 2 ? "0" : $boleto->getMoraDia(), 4, 2));
        $this->add(178, 183, $boleto->getJurosApos() === false ? '000000' : $boleto->getDataVencimento()->copy()->addDays($boleto->getJurosApos())->format('dmy'));
        $this->add(184, 184, $boleto->getModoDesconto());
        $this->add(185, 197, Util::formatCnab('9', (in_array($boleto->getModoDesconto(), [1, 2, 3])) ? $boleto->getDesconto() : 0, 13, 2));
        $this->add(198, 201, Util::formatCnab('9', (in_array($boleto->getModoDesconto(), [4, 5, 6])) ? $boleto->getDesconto() : 0, 2, 2));
        $this->add(202, 207, $boleto->getModoDesconto() > 0 ? $boleto->getDataDesconto()->format('dmy') : "000000");
        $this->add(208, 220, Util::formatCnab('9', $boleto->getValorAbatimento(), 13, 2));

        $this->add(221, 222, strlen(Util::onlyNumbers($boleto->getPagador()->getDocumento())) == 14 ? '02' : '01');
        $this->add(223, 236, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getDocumento()), 14));
        $this->add(237, 276, Util::formatCnab('X', $boleto->getPagador()->getNome(), 40));
        $this->add(277, 316, Util::formatCnab('X', $boleto->getPagador()->getEndereco(), 40));
        $this->add(317, 324, Util::formatCnab('9', Util::onlyNumbers($boleto->getPagador()->getCep()), 8));
        $this->add(325, 394, Util::formatCnab('X', '', 70));
        $this->add(395, 400, Util::formatCnab('9', $this->iRegistros + 1, 6));

        /* Verifica multa
        if ($boleto->getMulta() > 0) {
            // Inicia uma nova linha de detalhe e marca com a atual de edição
            $this->iniciaDetalhe();
            // Campo adicional para a multa
            $this->add(1, 1, 2); // Adicional Multa
            $this->add(2, 2, 2); // Cód 2 = Informa Valor em percentual
            $this->add(3, 10, $boleto->getDataVencimento()->format('dmY')); // Data da multa
            $this->add(11, 23, Util::formatCnab('9', Util::nFloat($boleto->getMulta(), 2), 13));
            $this->add(24, 394, '');
            $this->add(395, 400, Util::formatCnab('9', $this->iRegistros + 1, 6));
        }*/

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function trailer()
    {
        $this->iniciaTrailer();

        $this->add(1, 1, '9');
        $this->add(2, 7, Util::formatCnab('9', count($this->boletos), 6));
        $this->add(8, 394, '');
        $this->add(395, 400, Util::formatCnab('9', $this->getCount(), 6));

        return $this;
    }
}
