<?php
require 'autoload.php';
$beneficiario = new \Eduardokum\LaravelBoleto\Pessoa(
    [
        'nome'      => 'ACME',
        'endereco'  => 'Rua um 123',
        'bairro'    => 'Bairro',
        'cep'       => '99999-999',
        'uf'        => 'UF',
        'cidade'    => 'CIDADE',
        'documento' => '99.999.999/9999-99',
    ]
);

$pagador = new \Eduardokum\LaravelBoleto\Pessoa(
    [
        'nome'      => 'Cliente',
        'endereco'  => 'Rua um, 123',
        'bairro'    => 'Bairro',
        'cep'       => '99999-999',
        'uf'        => 'UF',
        'cidade'    => 'CIDADE',
        'documento' => '999.999.999-99',
    ]
);

$boleto = new Eduardokum\LaravelBoleto\Boleto\Banco\Inter(
    [
        'logo'                   => realpath(__DIR__ . '/../logos/') . DIRECTORY_SEPARATOR . '077.png',
        'modoMulta'              => Eduardokum\LaravelBoleto\Boleto\Banco\Inter::MULTA_PERCENTUAL,
        'modoMoraDia'            => Eduardokum\LaravelBoleto\Boleto\Banco\Inter::MORA_DIA_PERCENTUAL,
        'modoDesconto'           => Eduardokum\LaravelBoleto\Boleto\Banco\Inter::DESCONTO_PERCENTUAL_ATE_DATA_INFORMADA,
        'dataVencimento'         => new \Carbon\Carbon(),
        'maxDiasVencidos'        => 60,
        'valor'                  => 100,
        'valorAbatimento'        => 10.32,
        'desconto'               => 0.02,
        'multa'                  => 1.05,
        'juros'                  => 2,
        'numero'                 => 1,
        'numeroDocumento'        => 1,
        'pagador'                => $pagador,
        'beneficiario'           => $beneficiario,
        'diasBaixaAutomatica'    => 2,
        'carteira'               => 112,
        'agencia'                => 1111,
        'conta'                  => 99999,
        'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
        'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],        
        'especieDoc'             => 'DM',
    ]
);

$pdf = new Eduardokum\LaravelBoleto\Boleto\Render\HtmlInter();
$pdf->addBoleto($boleto);
echo $pdf->gerarBoleto();
