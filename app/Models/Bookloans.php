<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

    class Bookloans extends Model
    {
		/** Constans **/
		const max_loan = 3; //maximum allowed books to be borrowed
		const max_days = 14; // max days a can book can be borrowed
		
		/**
		 * The table associated with the model.
		 *
		 * @var string
		 */
		protected $table = 'book_loans';
		
		/**
		 * The attributes that should be mutated to dates.
		 *
		 * @var array
		 */
		public $timestamps = false;
		
		use conditionCombination;
		
		/**
		* Book checkout validation / registration(entry) process.
		* @param
		* @return mixed
		**/
		public function scopeCheckout($query, $request){
			$post_data = $request->all();
			$error = $this->maxLoanValidation($post_data['card_no']);
			if($error === false){
				$bookbranch_array = explode("-",$post_data['isbn']);
				$rows = $this->availabilityInBranch($bookbranch_array);
				if($rows > 0){
					$array = array(
								'book_id' =>$bookbranch_array[0],
								'branch_id' =>$bookbranch_array[1],
								'card_no' =>$post_data['card_no'],
								'date_out' =>date('Y-m-d'),
								'due_date' =>date('Y-m-d', strtotime("+".static::max_days." days"))
							);
					$query->insert($array);
					return true;
				}
				else
					$error = 'no_books';
			}
			return $error;
		}
		
		/**
		* Book check in update process.
		* @param
		* @return mixed
		**/
		public function scopeCheckin($query, $request){
			$post_data = $request->all();
			$query->where('id',$post_data['isbn']);
			$query->update(['date_in' => $post_data['date_in']]);
			return true;
		}
		/**
		* Maximum allowed books and card number validation.
		* @param
		* @return mixed
		**/
		protected function maxLoanValidation($card_no){
			$row = DB::table('borrower')->select(['borrower.card_no',DB::raw('count(book_id)as books_on_loan')])
				->leftJoin('book_loans','book_loans.card_no', '=', 'borrower.card_no')
				->where('borrower.card_no', '=' ,$card_no)
				->whereNull('date_in')
				->get();
			if(!$row[0]->card_no)
				return 'invalid_card';
			else if($row[0]->books_on_loan >= static::max_loan)
				return 'max_loan';
			else
				return false;

		}
		/**
		* Book availability in the specific selected branch.
		* @param
		* @return mixed
		**/
		protected function availabilityInBranch($bookbranch_array){
			$row = DB::table('book_copies')
				->select(['book_copies.book_id', DB::raw('count(book_loans.book_id)as books_on_loan'), 
					'no_of_copies', 'book_copies.branch_id'])
				->leftJoin('book_loans','book_loans.book_id', '=', DB::raw('book_copies.book_id  
						AND book_loans.branch_id = book_copies.branch_id AND book_loans.date_in is null'))
				->where('book_copies.book_id', '=', $bookbranch_array[0])
				->where('book_copies.branch_id', '=', $bookbranch_array[1])
				//->whereNull('book_loans.date_in')
				->groupby('book_id')
				->groupby('branch_id')
				->get();
			return $row[0]->no_of_copies-$row[0]->books_on_loan;
		}
		
		/**
		* Search loans from DB which has date_in is null.
		* @param
		* @return array
		**/
		public function scopeSearchLoans($query, $request){
			
			$data = $request->all();
			
			$rawConditionArray = array(array('isbn'=>'book_loans.book_id'), 
												array('card_no' => 'borrower.card_no'),
											array('borrower'=>array('borrower.first_name','borrower.last_name'))
										);
			$conditions = array('isbn_condition','title_condition');
			$this->prepareConditionCombination($rawConditionArray, $conditions, $query, $data);
			
			$sql = $query->select(['book_loans.id', 'book_loans.book_id',DB::raw('CONCAT(borrower.first_name,borrower.last_name) as name'),
									'due_date','book_loans.card_no','library_branch.branch_name','library_branch.branch_id'])
				->leftJoin('borrower','borrower.card_no', '=', 'book_loans.card_no')
				->leftJoin('books','books.book_id', '=', 'book_loans.book_id')
				->leftJoin('library_branch','book_loans.branch_id', '=', 'library_branch.branch_id')
				->where('date_in');
			return $sql;
		}
    }
