@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
		
            <div class="panel panel-default">
			<form name="search_books" method="get" action="{{ url('search') }}">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				
                <div class="panel-heading">Search Books</div>
				{{ csrf_field() }}
					<div class="panel-body">
						<div class="pad10">
							<span>ISBN :</span>
							<span>
								<input type="text" name="isbn" /> 
							</span>
							<div class="pad10">
								<select name="isbn_condition">
									<option value="0">OR</option>
									<option value="1">AND</option>
								</select>
							</div>
						</div>
						<div class="pad10">
							<span>Title :</span>
							<span>
								<input type="text" name="title" /> 						
							</span>
							<div class="pad10">
								<select name="title_condition">
									<option value="0">OR</option>
									<option value="1">AND</option>
								</select>
							</div>
						</div>
						<div class="pad10">
							<span>Author :</span>
							<span>
								<input type="text" name="author" />
							</span>
						</div>
						<div class="pad10">
							<span></span>
							<span>
								<input type="submit" name="Search Books!" />
							</span>
						</div>
					</div>
			</form>
				<div align="center" class="pad10">
					@if(isset($books) && count($books))
						{{ $books->appends(Request::except('page'))->links() }}
						<form id="loans">
						<table border="1" bordercolor="#D0D0D0" cellpadding="5" cellspacing="0">
							<thead>
							<tr>
								<th></th>
								<th class="pad10all">ISBN</th>
								<th class="pad10all">Branch Name</th>
								<th class="pad10all">Author</th>
								<th class="pad10all">Title</th>
								<th class="pad10all">Copies Available</th>
							</tr>
							</thead>
							<tbody>
							@foreach($books as $book)
								<tr>
									<td class="pad10all"><input type="radio" name="isbn" value="{{$book['book_id']}}-{{$book['branch_id']}}"></td>
									<td class="pad10all">{{ $book['book_id'] }}</td>
									<td class="pad10all">{{ $book['branch_name'] }}</td>
									<td class="pad10all">{{ $book['authors'] }}</td>
									<td class="pad10all">{{ $book['title'] }}</td>
									<td class="pad10all">{{ $book['no_of_copies'] }}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
						{{ $books->appends(Request::except('page'))->links() }}
						
							<div class="pad10">
							{{ csrf_field() }}
								<input type="text" name="card_no" />
								<input type="button" name="mysubmit" value="Check out!" />
								<div class="red" id="message"></div>
							</div>
						</form>
					@endif
				</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function(){
		jQuery("[type='button'][name='mysubmit']").click(function(){
			console.log(jQuery('form#loans').serialize());
			jQuery('#message').html('processing please wait....');
			jQuery.ajax({
				method:'post',
				data:jQuery('form#loans').serialize(),
				url:'checkout',
				dataType:'json',
				success:function(result,status,xhr){
					console.log(status);
					if(result.error){
						jQuery('#message').html(result.error);return;
					}
					switch(result.result){
						case 'no_books':
							jQuery('#message').html('Book currently not available in the branch.');
							break;
						case 'invalid_card':
							jQuery('#message').html('Please check the card number.');
							break;
						case 'max_loan':
							jQuery('#message').html('Card holder exceeded the maximum borrow limit.');
							break;
						default:
							jQuery('#message').html('Checked in successfully!');
					}
				},error: function(r,e){
					if(r.status = 422){
						jQuery('#message').html('');
						jQuery.each(r.responseJSON,function(i,t){
							jQuery('#message').append(t+'<br />');
						});
					}
				}
			});
		});
	});
//]]>
</script>
@endsection


