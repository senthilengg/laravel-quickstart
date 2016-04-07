@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
		
            <div class="panel panel-default">
			<form name="search_books" method="get" action="{{ url('searchloans') }}">
				@if (count($errors) > 0)
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif
				
                <div class="panel-heading">Search Loans</div>
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
							<span>Card No :</span>
							<span>
								<input type="text" name="card_no" /> 						
							</span>
							<div class="pad10">
								<select name="title_condition">
									<option value="0">OR</option>
									<option value="1">AND</option>
								</select>
							</div>
						</div>
						<div class="pad10">
							<span>Borrower Name :</span>
							<span>
								<input type="text" name="borrower" />
							</span>
						</div>
						<div class="pad10">
							<span></span>
							<span>
								<input type="submit" name="Search Loan!" />
							</span>
						</div>
					</div>
			</form>
				<div align="center" class="pad10">
					@if(isset($loans) && count($loans))
						{{ $loans->appends(Request::except('page'))->links() }}
						<form id="loans">
						<table border="1" bordercolor="#D0D0D0" cellpadding="5" cellspacing="0">
							<thead>
							<tr>
								<th></th>
								<th class="pad10all">ISBN</th>
								<th class="pad10all">Card No</th>
								<th class="pad10all">Name</th>
								<th class="pad10all">Branch Name</th>
								<th class="pad10all">Due Date</th>
							</tr>
							</thead>
							<tbody>
							@foreach($loans as $loan)
								<tr>
									<td class="pad10all"><input type="radio" name="isbn" value="{{$loan['id']}}"></td>
									<td class="pad10all">{{ $loan['book_id'] }}</td>
									<td class="pad10all">{{ $loan['card_no'] }}</td>
									<td class="pad10all">{{ $loan['name'] }}</td>
									<td class="pad10all">{{ $loan['branch_name'] }}</td>
									<td class="pad10all">{{ $loan['due_date'] }}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
						{{ $loans->appends(Request::except('page'))->links() }}
						
							<div class="pad10">
							{{ csrf_field() }}
								<input type="text" name="date_in" readonly="readonly" value="{{date('Y-m-d')}}" />
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
			jQuery('#message').html('processing please wait....');
			jQuery.ajax({
				method:'post',
				data:jQuery('form#loans').serialize(),
				url:'checkin',
				dataType:'json',
				success:function(result,status,xhr){
					if(result.error){
						jQuery('#message').html(result.error);return;
					}else{
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


