package com.paypal.example {
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import spark.primitives.BitmapImage 
	
	public class Application {
		public static var application :Application;
		
		private var shoppingCart :Cart;
		
		public function Application() {
		}
		
		/**
		 * Recupera a instância do Carrinho de compras
		 */
		public function cart() :Cart {
			if ( shoppingCart == null ) {
				shoppingCart = new Cart();
			}
			
			return shoppingCart;
		}
		
		/**
		 * Recupera a instância da aplicação
		 */
		public static function instance() :Application {
			if ( application == null ) {
				application = new Application();
			}
			
			return application;
		}
		
		/**
		 * Carrega e faz cache de uma imagem para melhorar o desempenho
		 * da aplicação.
		 */
		public function loadImage( url :String , image :BitmapImage ) :String {
			var name :String = url.substr( url.lastIndexOf( "/" ) + 1 , url.length );
			var local :File = File.applicationStorageDirectory.resolvePath( name );
			
			if ( local.exists ) {
				image.source = local.url;
			} else {
				var loader :URLLoader = new URLLoader();
				
				loader.dataFormat = URLLoaderDataFormat.BINARY;
				loader.load( new URLRequest( url ) );
				loader.addEventListener( Event.COMPLETE , function( e :Event ) :void {
					var stream :FileStream = new FileStream();
					
					stream.open( local , FileMode.WRITE );
					stream.writeBytes( loader.data );
					stream.close();
					
					image.source = local.url;
				} );
			}
			
			return name;
		}
	}
}