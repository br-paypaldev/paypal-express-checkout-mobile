<?php
require 'util.php';

$total = filter_input( INPUT_POST , 'total' , FILTER_VALIDATE_FLOAT );
$user = 'usuario-do-vendedos';
$pswd = 'senha';
$signature = 'ASSINATURA';

if ( is_float( $total ) ) {
	$baseURL = 'http://dominio.com.br/paypal';
	$responseNvp = call( $user , $pswd , $signature,
		'SetExpressCheckout' , array(
			'PAYMENTREQUEST_0_AMT'				=> sprintf( '%.02f' , $total ),
			'PAYMENTREQUEST_0_PAYMENTACTION'	=> 'Sale',
			'PAYMENTREQUEST_0_CURRENCYCODE'		=> 'BRL',
			'LOCALECODE'						=> 'pt_BR',
			'RETURNURL'							=> $baseURL . '/retorno.php',
			'CANCELURL'							=> $baseURL . '/cancelamento.php',
		) , true
	);

	if ( isset( $responseNvp[ 'ACK' ] ) && $responseNvp[ 'ACK' ] == 'Success' ) {
		showOutput( $responseNvp[ 'TOKEN' ] );
	} else {
		showOutput( 'OPZ' );
	}
} else {
	showOutput( 'OPZ' );
}
