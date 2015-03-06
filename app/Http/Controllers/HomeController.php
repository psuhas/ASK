<?php namespace App\Http\Controllers;

use MoHippo\Repositories\ProductRepository;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	private $repository;
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(ProductRepository $repository)
	{
		$this->repository = $repository;
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = $this->repository->paginate('book');
		return view('home' , array('data'=>$data));
	}

}
