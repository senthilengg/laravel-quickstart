# Laravel Quickstart
The PHP Framework For Web Artisans

This repository holds the controller, model and views of a library management portal with search books, book loans, checkout and checkin feature which will give you an idea to quickstart laravel. 

Prerequisites : Before going through this repository make sure you have working laravel installation in your localhost. ( Keep in mind that you need to run composer to make it work.)

Visit https://laravel.com/docs/5.2 for Laravel official documentation.


# Routes

There are different types of routes and you can create your own routes.

Web Middleware needs to be used if you are going to deal with sessions.

```php
#app/http/routes.php

Route::group(['middleware' => ['web']], function () {
    //
});

```

# Controllers
Before creatring the controller lets instruct laravel to look for the URI and specify the controller to route for. 
```php
#app/http/routes.php
Route::group(['middleware' => ['web']], function () {
  Route::get('/search', 'Books\SearchController@index');
});
```
Then lets create a controller.

Yoc can create controller manually or you can create using php artisan comment

```
php artisan make:controller Books\SearchController
```
If you are manually creating the controller make sure you have the below four lines of code.

```php
namespace App\Http\Controllers\Books; # defining namespace 
use App\Http\Controllers\Controller;  # inheriting base class 
use App\Http\Requests; # Required to access Http Request params 
use Illuminate\Http\Request; # Required to access Http Request params 
```

Now create your index function which calls the laravel welcome page.

Laravel uses a template engine called blade. Given that the template files needs to be suffixed with blade.php

```php
  public function index()
  {
    return view('welcome'); #calls laravel/resources/views/welcome.blade.php
  }
```

Now hit http://<your_localhost>/<laravel_path>/search should render laravel welcome page.
>if success

Import the database in your localhost. Database can be found in the DB folder provided.
>mysql DB

You can also try using php artisan migrate command to import DB in laravel way. But for now import via command panel or via phpmyadmin whichever you are comfortable with.
>on import success

Let configure the database details
Side note : Laravel supportsMySQL,Postgres,SQLite,SQL Server.

You can find .env file in your laravel root path if you are installed using composer else rename .env.example to .env then configure your database details.
>all set to access DB now.

Now lets change the update function top accept request
```
#https://github.com/senthilengg05/laravel-quickstart/blob/master/app/http/Controllers/Books/SearchController.php

  public function index(Request $request)
  {
  	
  }
```
i) Function argument **Request $request** gives the function the ability to access request params.

ii) Lets render a form from blade template 
```php
  public function index(Request $request)
  {
  	$books = [];
  	return view('books.search', ['books' => $books]); #books.search -- books folders and search.blade.php
  }
```
Then have a look at laravel-quickstart/resources/views/books/search.blade.php. Go through blade laravel documentation for depth understanding.

> Some points to remember
 1) {{ url('search') }} - Generates form action URL as per specification in routes.php 
 2) {{ csrf_field() }} -- this renders a hidden input type with a random string which will be matched up on each request submit by laravel to prevent cross site request forgery.
 3) {{ $books->appends(Request::except('page'))->links() }} -- Create pagination links and *Request::except('page')* appends the URI except the page=? with the pagination links. $books is the object we pass to the view from controller.

iii) Now lets validates the input (if you hit the page form should render now ). We are going to utlize the capability of laravel validation class
``` php

$validation = $this->validate($request, [
  		'isbn' => 'required_without_all:author,title',
  		'card_no' => 'required_without_all:isbn,title',
  		'borrower' => 'required_without_all:isbn,author',
  		]);
# isbn,card_no,borrower are the field names. Validation check anyone of these three presents
#Now the function becomes

  public function index(Request $request)
  {
  	$books = [];
  	if(count($request->all())){ #condition to restrict if the params not present.
  		$validation = $this->validate($request, [
  		'isbn' => 'required_without_all:author,title',
  		'card_no' => 'required_without_all:isbn,title',
  		'borrower' => 'required_without_all:isbn,author',
  		]);
  	}
  	return view('books.search', ['books' => $books]);
  }
```
iv) Lets make the controller talk to model/db here. So you have to create a model now. 

similar to controller you can use php artisan command to create models.

Have a look at the model here in laravel-quickstart/app/Models/Books.php and call the model scopeSearchBooks.

```php
#scopeSearchBooks is the function that below line calls and does the pagination as well.
Books::searchBooks($request)->paginate(20);
#Now the controller index function became
 public function index(Request $request)
  {
  	$books = [];
  	if(count($request->all())){ #condition to restrict if the params not present.
  		$validation = $this->validate($request, [
  		'isbn' => 'required_without_all:author,title',
  		'card_no' => 'required_without_all:isbn,title',
  		'borrower' => 'required_without_all:isbn,author',
  		]);
  		Books::searchBooks($request)->paginate(20);#passing $request to the model
  	}
  	return view('books.search', ['books' => $books]);
  }

#in Model
public function scopeSearchBooks($query, $request){} 
#first argument will be always the DB query object.
```
Since its a search we are using the custom function which needs to be prefixed with the word *scope*
To retrive from a single table you can use *Books::all()*
> Note : By default laravel consider the model name as table name as well. if you want to change use **protected $table = '<table_name>';**
To sucessfully call the model from the controller you have to include the specific model's namespace. In our case its 
```php
use App\Models\Books;
```
Laravel by default tries to update the timestamps of updation creation in the tables. So it look for created_at and updated_at columns., If you want to stop that just specify this in your class **public $timestamps = false;**

Similarly you can self learn joins,insert,update and json response for ajax calls from the features that has been provided in this repoository.

*Enjoy coding with laravel. Please rate my repository if you feel its worth doing so :)*













