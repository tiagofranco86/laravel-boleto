<?php

namespace Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab400\Banco;

use Eduardokum\LaravelBoleto\Util;
use Eduardokum\LaravelBoleto\Contracts\Cnab\RetornoCnab400;
use Eduardokum\LaravelBoleto\Cnab\Retorno\Cnab400\AbstractRetorno;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Illuminate\Support\Arr;

class Inter extends AbstractRetorno implements RetornoCnab400
{
    /**
     * Código do banco
     *
     * @var string
     */
    protected $codigoBanco = BoletoContract::COD_BANCO_INTER;

    /**
     * Array com as ocorrencias do banco;
     *
     * @var array
     */
    private $ocorrencias = [
        '02' => 'Entrada confirmada',
        '03' => 'Entrada rejeitada',
        '04' => 'Alteração de dados - baixa',
        '05' => 'Alteração de dados – baixa',
    ];

    /**
     * Array com as possiveis rejeicoes do banco.
     *
     * @var array
     */
    private $rejeicoes = [
        '79' => 'Retorno Abatimento Cancelado',
        '80' => 'Retorno Abatimento Concedido',
        '81' => 'Retorno Acerto Controle Participante',
        '82' => 'Retorno Acerto Dados Rateio Credito',
        '83' => 'Retorno Acerto Depositaria',
        '84' => 'Retorno Aguardando Autorizacao Protesto Edital',
        '85' => 'Retorno Alegacao DoSacado',
        '86' => 'Retorno Alteracao Dados Baixa',
        '87' => 'Retorno Alteracao Dados Nova Entrada',
        '88' => 'Retorno Alteracao Dados Rejeitados',
        '89' => 'Retorno Alteracao Data Emissao',
        '90' => 'Retorno Alteracao Especie',
        '91' => 'Retorno Alteracao Instrucao',
        '92' => 'Retorno Alteracao Opcao Devolucao Para Protesto Confirmada',
        '93' => 'Retorno Alteracao Opcao Protesto Para Devolucao Confirmada',
        '94' => 'Retorno Alteracao Outros Dados Rejeitada',
        '95' => 'Retorno Alteracao Reemissao Bloqueto Confirmada',
        '96' => 'Retorno Alteracao Seu Numero',
        '97' => 'Retorno Alteracao Uso Cedente',
        '98' => 'Retorno Alterar Data Desconto',
        '99' => 'Retorno Alterar Prazo Limite Recebimento',
        '10' => 'Retorno Alterar Sacador Avalista',
        '101' => 'Retorno Baixa Automatica',
        '102' => 'Retorno Baixa Credito CC Atraves Sispag',
        '103' => 'Retorno Baixa Credito CC Atraves Sispag Sem Titulo Corresp',
        '104' => 'Retorno Baixado',
        '105' => 'Retorno Baixado FrancoPagamento',
        '106' => 'Retorno Baixado InstAgencia',
        '107' => 'Retorno Baixado Por Devolucao',
        '108' => 'Retorno Baixado Via Arquivo',
        '109' => 'Retorno Baixa Liquidado Edital',
        '110' => 'Retorno Baixa Manual Confirmada',
        '111' => 'Retorno Baixa Ou Liquidacao Estornada',
        '112' => 'Retorno Baixa Por Protesto',
        '113' => 'Retorno Baixa Por Ter Sido Liquidado',
        '114' => 'Retorno Baixa Rejeitada',
        '115' => 'Retorno Baixa Simples',
        '116' => 'Retorno Baixa Solicitada',
        '117' => 'Retorno Baixa Titulo Negativado Sem Protesto',
        '118' => 'Retorno Baixa Transferencia Para Desconto',
        '119' => 'Retorno Cancelamento Dados Rateio',
        '120' => 'Retorno Cheque Compensado',
        '121' => 'Retorno Cheque Devolvido',
        '122' => 'Retorno Cheque Pendente Compensacao',
        '123' => 'Retorno Cobranca Contratual',
        '124' => 'Retorno Cobranca Creditar',
        '125' => 'Retorno Comando Recusado',
        '126' => 'Retorno Conf Cancelamento Negativacao Expressa Tarifa',
        '127' => 'Retorno Conf Entrada Negativacao Expressa Tarifa',
        '128' => 'Retorno Conf Exclusao Entrada Negativacao Expressa Por Liquidacao Tarifa',
        '129' => 'Retorno Conf Instrucao Transferencia Carteira Modalidade Cobranca',
        '130' => 'Retorno Confirmacao Alteracao Banco Sacado',
        '131' => 'Retorno Confirmacao Alteracao Juros Mora',
        '132' => 'Retorno Confirmacao Email SMS',
        '133' => 'Retorno Confirmacao Entrada Cobranca Simples',
        '134' => 'Retorno Confirmacao Exclusao Banco Sacado',
        '135' => 'Retorno Confirmacao Inclusao Banco Sacado',
        '136' => 'Retorno Confirmacao Pedido Excl Negativacao',
        '137' => 'Retorno Confirmacao Receb Pedido Negativacao',
        '138' => 'Retorno Confirma Recebimento Instrucao NaoNegativar',
        '139' => 'Retorno Conf Recebimento Inst Cancelamento Negativacao Expressa',
        '140' => 'Retorno Conf Recebimento Inst Entrada Negativacao Expressa',
        '141' => 'Retorno Conf Recebimento Inst Exclusao Entrada Negativacao Expressa',
        '142' => 'Retorno Custas Cartorio',
        '143' => 'Retorno Custas Cartorio Distribuidor',
        '144' => 'Retorno Custas Edital',
        '145' => 'Retorno Custas Irregularidade',
        '146' => 'Retorno Custas Protesto',
        '147' => 'Retorno Custas Sustacao',
        '148' => 'Retorno Custas Sustacao Judicial',
        '149' => 'Retorno Dados Alterados',
        '150' => 'Retorno Debito Custas Antecipadas',
        '151' => 'Retorno Debito Direto Autorizado',
        '152' => 'Retorno Debito Direto NaoAutorizado',
        '153' => 'Retorno Debito Em Conta',
        '154' => 'Retorno Debito Mensal Tarifa Aviso Movimentacao Titulos',
        '155' => 'Retorno Debito Mensal Tarifas Extrado Posicao',
        '156' => 'Retorno Debito Mensal Tarifas Manutencao Titulos Vencidos',
        '157' => 'Retorno Debito Mensal Tarifas Outras Instrucoes',
        '158' => 'Retorno Debito Mensal Tarifas Outras Ocorrencias',
        '159' => 'Retorno Debito Mensal Tarifas Protestos',
        '160' => 'Retorno Debito Mensal Tarifas SustacaoProtestos',
        '161' => 'Retorno Debito Tarifas',
        '162' => 'Retorno Desagendamento Debito Automatico',
        '163' => 'Retorno Desconto Cancelado',
        '164' => 'Retorno Desconto Concedido',
        '165' => 'Retorno Desconto Retificado',
        '166' => 'Retorno Despesa Cartorio',
        '167' => 'Retorno Despesas Protesto',
        '168' => 'Retorno Despesas Sustacao Protesto',
        '169' => 'Retorno Devolvido Pelo Cartorio',
        '170' => 'Retorno Dispensar Indexador',
        '171' => 'Retorno Dispensar Prazo Limite Recebimento',
        '172' => 'Retorno Email SMS Rejeitado',
        '173' => 'Retorno Emissao Bloqueto Banco Sacado',
        '174' => 'Retorno Encaminhado A Cartorio',
        '175' => 'Retorno Endereco Sacado Alterado',
        '176' => 'Retorno Entrada Bordero Manual',
        '177' => 'Retorno Entrada Confirmada Rateio Credito',
        '178' => 'Retorno Entrada Em Cartorio',
        '179' => 'Retorno Entrada Registrada Aguardando Avaliacao',
        '180' => 'Retorno Entrada Rejeita CEP Irregular',
        '181' => 'Retorno Entrada Rejeitada Carne',
        '182' => 'Retorno Entrada Titulo Banco Sacado Rejeitada',
        '183' => 'Retorno Equalizacao Vendor',
        '184' => 'Retorno Estorno Baixa Liquidacao',
        '185' => 'Retorno Estorno Pagamento',
        '186' => 'Retorno Estorno Protesto',
        '187' => 'Retorno Instrucao Cancelada',
        '188' => 'Retorno Instrucao Negativacao Expressa Rejeitada',
        '189' => 'Retorno Instrucao Protesto Rejeitada Sustada Ou Pendente',
        '190' => 'Retorno Instrucao Rejeitada',
        '191' => 'Retorno IOF Invalido',
        '192' => 'Retorno Juros Dispensados',
        '193' => 'Retorno Liquidado',
        '194' => 'Retorno Liquidado Apos Baixa Ou Nao Registro',
        '195' => 'Retorno Liquidado Em Cartorio',
        '196' => 'Retorno Liquidado Parcialmente',
        '197' => 'Retorno Liquidado PorConta',
        '198' => 'Retorno Liquidado Saldo Restante',
        '199' => 'Retorno Liquidado Sem Registro',
        '200' => 'Retorno Manutencao Banco Sacado Rejeitada',
        '201' => 'Retorno Manutencao Sacado Rejeitada',
        '202' => 'Retorno Manutencao Titulo Vencido',
        '203' => 'Retorno Negativacao Expressa Informacional',
        '204' => 'Retorno Nome Sacado Alterado',
        '205' => 'Retorno Ocorrencias Do Sacado',
        '206' => 'Retorno Outras Ocorrencias',
        '207' => 'Retorno Outras Tarifas Alteracao',
        '208' => 'Retorno Pagador DDA',
        '209' => 'Retorno Prazo Devolucao Alterado',
        '210' => 'Retorno Prazo Protesto Alterado',
        '211' => 'Retorno Protestado',
        '212' => 'Retorno Protesto Imediato Falencia',
        '213' => 'Retorno Protesto Ou Sustacao Estornado',
        '214' => 'Retorno Protesto Sustado',
        '215' => 'Retorno Recebimento Instrucao Alterar Dados',
        '216' => 'Retorno Recebimento Instrucao Alterar EnderecoSacado',
        '217' => 'Retorno Recebimento Instrucao Alterar Juros',
        '218' => 'Retorno Recebimento Instrucao Alterar NomeSacado',
        '219' => 'Retorno Recebimento Instrucao Alterar Tipo Cobranca',
        '220' => 'Retorno Recebimento Instrucao Alterar Valor Titulo',
        '221' => 'Retorno Recebimento Instrucao Alterar Vencimento',
        '222' => 'Retorno Recebimento Instrucao Baixar',
        '223' => 'Retorno Recebimento Instrucao Cancelar Abatimento',
        '224' => 'Retorno Recebimento Instrucao Cancelar Desconto',
        '225' => 'Retorno Recebimento Instrucao Conceder Abatimento',
        '226' => 'Retorno Recebimento Instrucao Conceder Desconto',
        '227' => 'Retorno Recebimento Instrucao Dispensar Juros',
        '228' => 'Retorno Recebimento Instrucao Nao Protestar',
        '229' => 'Retorno Recebimento Instrucao Protestar',
        '230' => 'Retorno Recebimento Instrucao Sustar Protesto',
        '231' => 'Retorno Reembolso Devolucao Desconto Vendor',
        '232' => 'Retorno Reembolso Nao Efetuado',
        '233' => 'Retorno Reembolso Transferencia Desconto Vendor',
        '234' => 'Retorno Registro Confirmado',
        '235' => 'Retorno Registro Recusado',
        '236' => 'Retorno Relacao De Titulos',
        '237' => 'Retorno Remessa Rejeitada',
        '238' => 'Retorno Reservado',
        '239' => 'Retorno Retirado De Cartorio',
        '240' => 'Retorno Segunda Via Instrumento Protesto',
        '241' => 'Retorno Segunda Via Instrumento Protesto Cartorio',
        '242' => 'Retorno Solicitacao Impressao Titulo Confirmada',
        '243' => 'Retorno Sustacao Envio Cartorio',
        '244' => 'Retorno Sustado Judicial',
        '245' => 'Retorno Tarifa Aviso Cobranca',
        '246' => 'Retorno Tarifa De Manutencao De Titulos Vencidos',
        '247' => 'Retorno Tarifa De Relacao Das Liquidacoes',
        '248' => 'Retorno Tarifa Email Cobranca Ativa Eletronica',
        '249' => 'Retorno Tarifa Emissao Aviso Movimentacao Titulos',
        '250' => 'Retorno Tarifa Emissao Boleto Envio Duplicata',
        '251' => 'Retorno Tarifa Extrato Posicao',
        '252' => 'Retorno Tarifa Instrucao',
        '253' => 'Retorno Tarifa Mensal Baixas Bancos Corresp Carteira',
        '254' => 'Retorno Tarifa Mensal Baixas Carteira',
        '255' => 'Retorno Tarifa Mensal Cancelamento Negativacao Expressa',
        '256' => 'Retorno Tarifa Mensal Email Cobranca AtivaEletronica',
        '257' => 'Retorno Tarifa Mensal Emissao Boleto Envio Duplicata',
        '258' => 'Retorno Tarifa Mensal Exclusao Entrada Negativacao Expressa',
        '259' => 'Retorno Tarifa Mensal Exclusao Negativacao Expressa Por Liquidacao',
        '260' => 'Retorno Tarifa Mensal Liquidacoes Bancos Corresp Carteira',
        '261' => 'Retorno Tarifa Mensal Liquidacoes Carteira',
        '262' => 'Retorno Tarifa Mensal Por Boleto Ate 03 Envio Cobranca Ativa Eletronica',
        '263' => 'Retorno Tarifa Mensal Ref Entradas Bancos Corresp Carteira',
        '264' => 'Retorno Tarifa Mensal SMS Cobranca Ativa Eletronica',
        '265' => 'Retorno Tarifa Ocorrencias',
        '266' => 'Retorno Tarifa Por Boleto Ate 03 Envio Cobranca Ativa Eletronica',
        '267' => 'Retorno Tarifa SMS Cobranca Ativa Eletronica',
        '268' => 'Retorno Tipo Cobranca Alterado',
        '269' => 'Retorno Titulo DDA Nao Reconhecido Pagador',
        '270' => 'Retorno Titulo DDA Reconhecido Pagador',
        '271' => 'Retorno Titulo DDA Recusado CIP',
        '272' => 'Retorno Titulo Em Ser',
        '273' => 'Retorno Titulo Ja Baixado',
        '274' => 'Retorno Titulo Nao Existe',
        '275' => 'Retorno Titulo Pagamento Cancelado',
        '276' => 'Retorno Titulo Pago Em Cheque',
        '277' => 'Retorno Titulo Sustado Judicialmente',
        '278' => 'Retorno Transferencia Carteira',
        '279' => 'Retorno Transferencia Carteira Baixa',
        '280' => 'Retorno Transferencia Carteira Entrada',
        '281' => 'Retorno Transferencia Cedente',
        '282' => 'Retorno Transito Pago Cartorio',
        '283' => 'Retorno Vencimento Alterado',
        '284' => 'Retorno Rejeicao Sacado',
        '285' => 'Retorno Aceite Sacado',
        '286' => 'Retorno Liquidado On Line',
        '287' => 'Retorno Estorno Liquidacao OnLine',
        '288' => 'Retorno Confirmacao Alteracao Valor Nominal',
        '289' => 'Retorno Confirmacao Alteracao Valor Percentual Minimo Maximo',
        '290' => 'Tipo Ocorrencia Nenhum',
        '291' => 'Retorno Confirmação de Recebimento de Pedido de Negativação',
        '292' => 'Retorno Confirmação de Recebimento de Pedido de Exclusão de Negativação',
        '293' => 'Retorno Confirmação de Entrada de Negativação',
        '294' => 'Retorno Entrada de Negativação Rejeitada',
        '295' => 'Retorno Confirmação de Exclusão de Negativação',
        '296' => 'Retorno Exlusão de Negativação Rejeitada',
        '297' => 'Retorno Exclusão e Negativação por Outros Motivos',
        '298' => 'Retorno Ocorrência Informacional por Outros Motivos',
        '299' => 'Retorno Inclusão de Negativação',
        '300' => 'Retorno Exclusão de Negativação',
        '301' => 'Retorno Em Transito',
        '302' => 'Retorno Liquidação em Condicional em Cartório Com Cheque do Próprio Devedor',
        '303' => 'Retorno Título Protestado Sustado Judicialmente em definitivo',
        '304' => 'Retorno Liquidação de Título Descontado',
        '305' => 'Retorno Protesto Em Cartório',
        '306' => 'Retorno Sustação Solicitada',
        '307' => 'Retorno Título Utilizado Como Garantia em Operação de Desconto',
        '308' => 'Retorno Título Descontável Com Desistência de Garantia em Operação de Desconto',
        '309' =>  'Retorno Intenção de Pagamento'
    ];

    /**
     * Roda antes dos metodos de processar
     */
    protected function init()
    {
        $this->totais = [
            'liquidados'  => 0,
            'entradas'    => 0,
            'baixados'    => 0,
            'protestados' => 0,
            'erros'       => 0,
            'alterados'   => 0,
        ];
    }

    /**
     * @param array $header
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarHeader(array $header)
    {
        $this->getHeader()
            ->setOperacaoCodigo($this->rem(2, 2, $header))
            ->setOperacao($this->rem(3, 9, $header))
            ->setServicoCodigo($this->rem(10, 11, $header))
            ->setServico($this->rem(12, 26, $header))
            ->setData($this->rem(95, 100, $header));

        return true;
    }

    /**
     * @param array $detalhe
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarDetalhe(array $detalhe)
    {
        $d = $this->detalheAtual();

        $d->setCarteira($this->rem(87, 89, $detalhe))
            ->setOcorrencia($this->rem(90, 91, $detalhe))

            ->setDataOcorrencia($this->rem(92, 97, $detalhe))
            ->setNumeroDocumento($this->rem(71, 81, $detalhe))
            ->setNumeroControle($this->rem(98, 107, $detalhe))
            ->setNossoNumero($this->rem(108, 118, $detalhe))
            ->setDataVencimento($this->rem(119, 124, $detalhe))
            ->setValor(Util::nFloat($this->rem(125, 137, $detalhe) / 100, 2, false))
            ->setValorRecebido(Util::nFloat($this->rem(160, 172, $detalhe) / 100, 2, false))
            ->setDataCredito($this->rem(173, 178, $detalhe));


        /**
         * ocorrencias
         */
        $msgAdicional = str_split(sprintf('%08s', $this->rem(90, 91, $detalhe)), 2) + array_fill(0, 5, '');
        if ($d->hasOcorrencia('02')) {
            $this->totais['entradas']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ENTRADA);
        } elseif ($d->hasOcorrencia('04')) {
            $this->totais['baixados']++;
            $d->setOcorrenciaTipo($d::OCORRENCIA_ALTERACAO);
        } elseif ($d->hasOcorrencia('03')) {
            $this->totais['erros']++;
            $error = Util::appendStrings(
                Arr::get($this->rejeicoes, $msgAdicional[0], ''),
                Arr::get($this->rejeicoes, $msgAdicional[1], ''),
                Arr::get($this->rejeicoes, $msgAdicional[2], ''),
                Arr::get($this->rejeicoes, $msgAdicional[3], '')
            );
            $d->setError($error);
        } else {
            $d->setOcorrenciaTipo($d::OCORRENCIA_OUTROS);
        }

        return true;
    }

    /**
     * @param array $trailer
     *
     * @return bool
     * @throws \Exception
     */
    protected function processarTrailer(array $trailer)
    {
        $this->getTrailer()
            ->setQuantidadeTitulos((int) $this->rem(18, 25, $trailer))
            ->setValorTitulos((float) Util::nFloat($this->rem(121, 132, $trailer) / 100, 2, false))
            ->setQuantidadeErros((int) $this->rem(87, 91, $trailer))
            ->setQuantidadeEntradas((int) $this->rem(58, 62, $trailer))
            ->setQuantidadeLiquidados((int) $this->rem(116, 120, $trailer))
            ->setQuantidadeBaixados((int) $this->totais['baixados'])
            ->setQuantidadeAlterados((int) $this->totais['alterados']);

        return true;
    }
}
