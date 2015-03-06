<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoHippo\Repositories\ProductRepository;
use MoHippo\AmazonProduct;

use Illuminate\Contracts\Cache\Repository as Cache;

class ProductServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(ProductRepository::class, function(){
			return new ProductRepository($this->app['cache.store'], new AmazonProduct());
		});
	}

}
