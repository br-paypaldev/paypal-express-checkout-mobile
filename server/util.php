<?php
/**
 * Exibe uma saída XML
 * @param	string $message Uma mensagem qualquer
 */
function showOutput( $message ) {
	header( 'Content-Type: text/xml; charset=UTF-8' );
	echo '<?xml version="1.0"?>' , PHP_EOL;
	echo '<response>';
	echo '<message>' , $message , '</message>';
	echo '</response>';
}

/**
 * Efetua uma chamada a uma operação do PayPal
 * @param	string $user Nome do usuário
 * @param	string $pwd Senha do usuário
 * @param	string $signature Assinatura de acesso
 * @param	string $operation Operação que será executada
 * @param	array $nvp Campos que serão enviados com a requisição
 * @param	boolean $close Indica se a conexão deverá ser fechada
 * @return	array Matriz associativa com os pares Nome=Valor retornados
 */
function call( $user , $pwd , $signature , $operation , array $nvp , $close = false ) {
	static $curl = null;

	$matches = array();
	$response = array();

	$nvp[ 'VERSION'		] = '64';
	$nvp[ 'METHOD'		] = $operation;
	$nvp[ 'PWD'			] = $pwd;
	$nvp[ 'USER'		] = $user;
	$nvp[ 'SIGNATURE'	] = $signature;

	if ( !is_resource( $curl ) ) {
		$curl = curl_init();
	}

	curl_setopt( $curl , CURLOPT_URL , 'https://api-3t.sandbox.paypal.com/nvp' );
	curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
	curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
	curl_setopt( $curl , CURLOPT_POST , 1 );
	curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

	if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , urldecode( curl_exec( $curl ) ) , $matches ) ) {
		foreach ( $matches[ 'name' ] as $offset => $name ) {
			$response[ $name ] = $matches[ 'value' ][ $offset ];
		}
	}

	if ( $close ) {
		curl_close( $curl );
	}

	return $response;
}