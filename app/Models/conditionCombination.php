<?php

namespace App\Models;

    trait conditionCombination
    {
		/**
		* Prepare Conditions combination.
		* Used in Books/Bookloans class
		* @param
		* @return Void
		**/
		public function prepareConditionCombination($rawConditionArray, $conditions=array(), $query, $data){
			$where = 'and';
			$query->where(function($query) use ($rawConditionArray,$conditions,$data,$where){
				foreach($rawConditionArray as $key => $input_name_column){
					foreach($input_name_column as $input_name=>$column){
						if(isset($data[$input_name]) && $data[$input_name] != ''){
							if($key > 0){
								if($data[$conditions[$key-1]] == 0)
									$where = 'or';
								else
									$where = 'and';
							}
							if($where == 'and'){
								$query->where($column, 'like', '%'.$data[$input_name].'%');
							}else{
								$query->orWhere($column, 'like', '%'.$data[$input_name].'%');
							}
						}
					}
				}
			});
		}
    }
