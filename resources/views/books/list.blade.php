@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Search Books</div>

                <div class="panel-body">
					<ul>
						@foreach ($books as $book)
							<li>{{ $book->title }}</li>
						@endforeach
					</ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
