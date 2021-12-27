<?php

namespace Eduardokum\LaravelBoleto\Boleto\Render;

use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Render\Pdf as PdfContract;
use Eduardokum\LaravelBoleto\Util;
use Illuminate\Support\Str;

class PdfInter extends Pdf
{    
    /**
     * @param integer $i
     *
     * @return $this
     */

     /**
     * Retorna o endereço completo em uma única string
     *
     * Ex.: Rua um, 123 - Bairro Industrial - Brasília - DF - 71000-000
     *
     * @return string
     */
    public function getEnderecoCompleto(BoletoContract $boleto)
    {
        $dados = array_filter(array($boleto->getBeneficiario()->getEndereco(), $boleto->getBeneficiario()->getBairro(), $boleto->getBeneficiario()->getCidade() . "-". $boleto->getBeneficiario()->getUf()));
        return strtoupper(implode(', ', $dados));
    }

    /**
     * @param integer $i
     *
     * @return $this
     */
    protected function Bottom($i)
    {
        $this->Image($this->boleto[$i]->getLogoBanco(), 20, ($this->GetY() - 2), 28);
        $this->Cell(29, 6, '', 'B');
        $this->SetFont($this->PadraoFont, 'B', 13);
        $this->Cell(15, 6, $this->boleto[$i]->getCodigoBancoComDv(), 'LBR', 0, 'C');
        $this->SetFont($this->PadraoFont, 'B', 10);
        $this->Cell(0, 6, $this->boleto[$i]->getLinhaDigitavel(), 'B', 1, 'R');

        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(120, $this->desc, $this->_('Local de pagamento'), 'TLR');
        $this->Cell(50, $this->desc, $this->_('Vencimento'), 'TR', 1);

        $this->SetFont($this->PadraoFont, 'B', $this->fcel);
        $this->Cell(120, $this->cell, $this->_($this->boleto[$i]->getLocalPagamento()), 'LR');
        $this->Cell(50, $this->cell, $this->_($this->boleto[$i]->getDataVencimento()->format('d/m/Y')), 'R', 1, 'R');

        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(120, $this->desc, $this->_('Beneficiário'), 'TLR');
        $this->Cell(50, $this->desc, $this->_('Agência/Código beneficiário'), 'TR', 1);

        $this->SetFont($this->PadraoFont, 'B', $this->fcel);
        $this->Cell(120, $this->cell, $this->_($this->boleto[$i]->getBeneficiario()->getNomeDocumento()), 'LR');
        
        $xBeneficiario = $this->GetX();
        $yBeneficiario = $this->GetY();
        $this->Cell(50, $this->cell, $this->_($this->boleto[$i]->getAgenciaCodigoBeneficiario()), 'R', 1, 'R');
        if($this->boleto[$i]->getMostrarEnderecoFichaCompensacao()) {
            $this->SetXY($xBeneficiario, $yBeneficiario);
            $this->Ln(4);
            $this->SetFont($this->PadraoFont, 'B', $this->fcel);
            $this->Cell(120, $this->cell, $this->_($this->boleto[$i]->getBeneficiario()->getEnderecoCompleto()), 'LR');
            $this->Cell(50, $this->cell, "", 'R', 1, 'R');
        }

        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(120, $this->desc, $this->_('Endereço do beneficiário'), 'TLR');
        $this->Cell(50, $this->desc, $this->_('Nosso número / Cód. do documento'), 'TR', 1, 'L');
        
        $this->SetFont($this->PadraoFont, 'B', $this->fcel);
        $this->Cell(120, $this->cell, $this->_($this->getEnderecoCompleto($this->boleto[$i])), 'LR', 0, 'L');
        $this->Cell(50, $this->cell, $this->_($this->boleto[$i]->getNossoNumeroBoleto()), 'R', 1, 'R');

        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(30, $this->desc, $this->_('Data do documento'), 'TLR');
        $this->Cell(40, $this->desc, $this->_('Número do documento'), 'TR');
        $this->Cell(15, $this->desc, $this->_('Espécie Doc.'), 'TR');
        $this->Cell(10, $this->desc, $this->_('Aceite'), 'TR');
        $this->Cell(25, $this->desc, $this->_('Data processamento'), 'TR');
        $this->Cell(50, $this->desc, $this->_('(=) Valor do Documento'), 'TR', 1);

        $this->SetFont($this->PadraoFont, 'B', $this->fcel);
        $this->Cell(30, $this->cell, $this->_($this->boleto[$i]->getDataDocumento()->format('d/m/Y')), 'LR');
        $this->Cell(40, $this->cell, $this->_($this->boleto[$i]->getNumeroDocumento()), 'R');
        $this->Cell(15, $this->cell, $this->_($this->boleto[$i]->getEspecieDoc()), 'R');
        $this->Cell(10, $this->cell, $this->_($this->boleto[$i]->getAceite()), 'R');
        $this->Cell(25, $this->cell, $this->_($this->boleto[$i]->getDataProcessamento()->format('d/m/Y')), 'R');
        $this->Cell(50, $this->cell, $this->_(Util::nReal($this->boleto[$i]->getValor())), 'R', 1, 'R');
        
        $this->SetFont($this->PadraoFont, '', $this->fdes);

        if (isset($this->boleto[$i]->variaveis_adicionais['esconde_uso_banco']) && $this->boleto[$i]->variaveis_adicionais['esconde_uso_banco']) {
            $this->Cell(55, $this->desc, $this->_('Carteira'), 'TLR');
        } else {
            $cip = isset($this->boleto[$i]->variaveis_adicionais['mostra_cip']) && $this->boleto[$i]->variaveis_adicionais['mostra_cip'];

            $this->Cell(($cip ? 23 : 30), $this->desc, $this->_('Uso do Banco'), 'TLR');
            if ($cip) {
                $this->Cell(7, $this->desc, $this->_('CIP'), 'TLR');
            }
            $this->Cell(25, $this->desc, $this->_('Carteira'), 'TR');
        }

        $this->Cell(12, $this->desc, $this->_('Espécie'), 'TR');
        $this->Cell(28, $this->desc, $this->_('Quantidade Moeda'), 'TR');
        $this->Cell(25, $this->desc, $this->_('Valor Moeda'), 'TR');
        $this->Cell(50, $this->desc, $this->_('(-) Desconto / Abatimentos)'), 'TR', 1);

        $this->SetFont($this->PadraoFont, 'B', $this->fcel);

        if (isset($this->boleto[$i]->variaveis_adicionais['esconde_uso_banco']) && $this->boleto[$i]->variaveis_adicionais['esconde_uso_banco']) {
            $this->TextFitCell(55, $this->cell, $this->_($this->boleto[$i]->getCarteiraNome()), 'LR', 0, 'L');
        } else {
            $cip = isset($this->boleto[$i]->variaveis_adicionais['mostra_cip']) && $this->boleto[$i]->variaveis_adicionais['mostra_cip'];
            $this->Cell(($cip ? 23 : 30), $this->cell, $this->_(''), 'LR');
            if ($cip) {
                $this->Cell(7, $this->cell, $this->_($this->boleto[$i]->getCip()), 'LR');
            }
            $this->Cell(25, $this->cell, $this->_(strtoupper($this->boleto[$i]->getCarteiraNome())), 'R');
        }

        $this->Cell(12, $this->cell, $this->_('R$'), 'R');
        $this->Cell(28, $this->cell, $this->_(''), 'R');
        $this->Cell(25, $this->cell, $this->_(''), 'R');
        $this->Cell(50, $this->cell, $this->_(''), 'R', 1, 'R');

        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(120, $this->desc, $this->_("Instruções de responsabilidade do beneficiário. Qualquer dúvida sobre este boleto, contate o beneficiário"), 'TLR');
        $this->Cell(50, $this->desc, $this->_('(-) Outras deduções'), 'TR', 1);

        $xInstrucoes = $this->GetX();
        $yInstrucoes = $this->GetY();

        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_('(+) Mora / Multa'), 'TR', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_('(+) Outros acréscimos'), 'TR', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_('(=) Valor cobrado'), 'TR', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->Cell(120, $this->desc, $this->_(''), 'LR');
        $this->Cell(50, $this->desc, $this->_(''), 'R', 1);

        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(18, $this->fdes, $this->_('Pagador'), 'TL', 0); 

        $this->SetFont($this->PadraoFont, 'B', $this->fcel);
        $this->Cell(0, $this->fdes, $this->_(strtoupper($this->boleto[$i]->getPagador()->getNome())), 'T', 0);
        $this->Cell(0, $this->fdes, "CNPJ/CPF: " .$this->_($this->boleto[$i]->getPagador()->getDocumento()), 'TR', 1, 'R', 1);
        $this->Cell(18, $this->cell, '', 'L', 0);
        $this->Cell(0, $this->cell, $this->_(strtoupper(trim($this->boleto[$i]->getPagador()->getEndereco() . ' - ' . $this->boleto[$i]->getPagador()->getBairro())), ' -'), 'R', 1);
        $this->Cell(18, $this->cell, '', 'L', 0);
        $this->Cell(0, $this->cell, $this->_(strtoupper($this->boleto[$i]->getPagador()->getCepCidadeUf())), 'R', 1);
        
        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(18, $this->fdes, $this->_('Beneficiário final'), 'L');     
        
        $this->SetFont($this->PadraoFont, 'B', $this->fcel);
        $this->Cell(0, $this->fdes, $this->_(strtoupper($this->boleto[$i]->getBeneficiario()->getNome())), '', 0);
        $this->Cell(0, $this->fdes, "CNPJ/CPF: " .$this->_(strtoupper($this->boleto[$i]->getBeneficiario()->getDocumento())), 'BR', 1, 'R', 1);
        
        $this->SetFont($this->PadraoFont, '', $this->fdes);
        $this->Cell(0, $this->desc, $this->_('Autenticação mecânica - Ficha de Compensação'), 'T', 1, 'R');

        $xOriginal = $this->GetX();
        $yOriginal = $this->GetY();

        if (count($this->boleto[$i]->getInstrucoes()) > 0) {
            $this->SetXY($xInstrucoes, $yInstrucoes);
            $this->Ln(1);
            $this->SetFont($this->PadraoFont, 'B', $this->fcel);

            $this->listaLinhas($this->boleto[$i]->getInstrucoes(), 0);

            $this->SetXY($xOriginal, $yOriginal);
        }
        return $this;
    }

    /**
     * @param integer $i
     */
    protected function codigoBarras($i)
    {
        $this->Ln(3);
        $this->Cell(0, 15, '', 0, 1, 'L');
        $this->i25($this->GetX(), $this->GetY() - 15, $this->boleto[$i]->getCodigoBarras(), 0.8, 17);
    }
}