<?php
require 'util.php';

if ( isset( $_GET[ 'token' ] ) ) {
	$token = $_GET[ 'token' ];
	$user = 'usuario-do-vendedor';
	$pswd = 'senha';
	$signature = 'ASSINATURA';

	$responseNvp = call( $user , $pswd , $signature,
		'GetExpressCheckoutDetails' , array(
			'TOKEN'	=> $token,
		)
	);

	if ( isset( $responseNvp[ 'TOKEN' ] ) && isset( $responseNvp[ 'ACK' ] ) ) {
		if ( $responseNvp[ 'TOKEN' ] == $token && $responseNvp[ 'ACK' ] == 'Success' ) {
			$responseNvp = call( $user , $pswd , $signature,
				'DoExpressCheckoutPayment' , array(
					'TOKEN'								=> $token,
					'PAYERID'							=> $responseNvp[ 'PAYERID' ],
					'PAYMENTREQUEST_0_AMT'				=> $responseNvp[ 'PAYMENTREQUEST_0_AMT' ],
					'PAYMENTREQUEST_0_CURRENCYCODE'		=> $responseNvp[ 'PAYMENTREQUEST_0_CURRENCYCODE' ],
					'PAYMENTREQUEST_0_PAYMENTACTION'	=> 'Sale',
				) , true
			);

			if ( isset( $responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] ) && $responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] == 'Completed' ) {
				showOutput( 'SUCESSO' );
			} else {
				showOutput( 'OPZ' );
			}
		} else {
			showOutput( 'OPZ' );
		}
	} else {
		showOutput( 'OPZ' );
	}
} else {
	showOutput( 'OPZ' );
}
