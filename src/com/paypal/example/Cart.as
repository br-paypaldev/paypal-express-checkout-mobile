package com.paypal.example {
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	
	public class Cart extends EventDispatcher {
		[Bindable]
		private var products :XML = new XML();
		
		[Bindable]
		public var total :Number = 0;
		
		public var token :String;
		
		public function Cart() {
			clean();
		}
		
		/**
		 * Adiciona um item ao carrinho
		 */
		public function add( item :XML ) :void {
			products.appendChild( item );
			
			total += parseFloat( item.price );
			dispatchEvent( new Event( "cartEvent" ) );
		}
		
		/**
		 * Compra diretamente um item
		 */
		public function buyNow( item :XML ) :void {
			clean();
			add( item );
			checkout();
		}
		
		/**
		 * Faz checkout do carrinho
		 */
		public function checkout() :void {
			var loader :URLLoader = new URLLoader();
			var request :URLRequest = new URLRequest( 'http://improjetos.com.br/paypal/checkout.php' );
			var variables :URLVariables = new URLVariables();

			variables.total = total;

			request.method = URLRequestMethod.POST;
			request.data = variables;

			loader.addEventListener( Event.COMPLETE , function( e :Event ) :void  {
				var xmlData :XML = XML( e.target.data );
				
				token = xmlData.message.children().toXMLString();
				
				if ( token == 'OPZ' ) {
					dispatchEvent( new Event( "OPZ" ) );
				} else {
					dispatchEvent( new Event( "SUCCESS" ) );
				}
			} );

			loader.addEventListener( IOErrorEvent.IO_ERROR , function( e :Event ) :void {
				dispatchEvent( new Event( "OPZ" ) );
			} );

			loader.load( request );
		}
		
		/**
		 * Limpa o carrinho
		 */
		public function clean() :void {
			products = XML( "<products></products>" );
			total = 0;
			
			dispatchEvent( new Event( "cartEvent" ) );
		}

		/**
		 * Recupera os itens do carrinho
		 */		
		[Bindable(event="cartEvent")]
		public function getItems() :XML {
			return products;
		}
	}
}