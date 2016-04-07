<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

    class Books extends Model
    {
		use conditionCombination;
		/**
		* Prepare the author sql query string to append
		* with main search query.
		* @param
		* @return string $authors_sql
		**/
		protected function authorSearch(){
			$authors_sql = DB::table('book_authors')
				->select(['book_id',DB::raw('group_concat(distinct author_name) as authors')])
				->groupby('book_id')->toSql();
			return DB::raw('('.$authors_sql.') as book_authors');
		}
		/**
		* Book search process happens here.
		* 
		* @param
		* @return array $result_array
		**/
		public function scopeSearchBooks($query, $request){
			$authors_sql = $this->authorSearch();
			$data = $request->all();
			$rawConditionArray = array(array('isbn'=>'books.book_id'), 
										array('title'=>'books.title'),
										array('author' => 'authors')
									);
			$conditions = array('isbn_condition','title_condition');
			$this->prepareConditionCombination($rawConditionArray, $conditions, $query, $data);		
			$sql = $query
				->select(['books.book_id', 'library_branch.branch_name', 
							'book_authors.authors', 'books.title', 'no_of_copies','library_branch.branch_id'])
				->leftJoin($authors_sql,'book_authors.book_id', '=', 'books.book_id')
				->leftJoin('book_copies','book_copies.book_id','=','books.book_id')
				->leftJoin('library_branch','library_branch.branch_id', '=','book_copies.branch_id')
				->whereNotNull('book_copies.branch_id');
			return $sql;
		}
    }
