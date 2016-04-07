<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Books;
use App\Models\Bookloans;
use Input;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$books = [];
		if(count($request->all())){
			$validation = $this->validate($request, [
			'isbn' => 'required_without_all:author,title',
			'card_no' => 'required_without_all:isbn,title',
			'borrower' => 'required_without_all:isbn,author',
			]);
			$books = Books::searchBooks($request)->paginate(20);
		}
		return view('books.search', ['books' => $books]);
    }
	
	/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchloans(Request $request)
    {
		$loans = [];
		if(count($request->all())){
			$validation = $this->validate($request, [
			'isbn' => 'required_without_all:card_no,borrower',
			'card_no' => 'required_without_all:isbn,borrower',
			'borrower' => 'required_without_all:isbn,card_no',
			]);
			$loans = Bookloans::searchLoans($request)->paginate(20);
		}
		return view('books.searchloans', ['loans' => $loans]);
    }
	
	/**
	* Check out books here.
	* @param
	* @return json
	**/
	public function checkout(Request $request){
		$post_data = $request->all();
		$this->validate($request, ['isbn' => 'required', 'card_no' => 'required']);
		$bookloans = new Bookloans;
        $data['result'] = $bookloans->checkout($request);
		return response()
            ->json($data);
	}
	
	/**
	* Check out books here.
	* @param
	* @return json
	**/
	public function checkin(Request $request){
		$post_data = $request->all();
		$messages = [
			'isbn.required' => 'Please select a book and ensure card number matches!',
			'date_in.required' => 'Please make sure the date is today\'s date!'
		];
		$this->validate($request, ['isbn' => 'required', 'date_in' => 'required'], $messages );
		$bookloans = new Bookloans;
        $data['result'] = $bookloans->checkin($request);
		return response()
            ->json($data);
	}
}
