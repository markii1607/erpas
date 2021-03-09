<?php 
    namespace App\Model\Report;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class BillOfQuantitiesQueryHandler extends QueryHandler {
    	/**
    	 * `selectProject` Query string that will select from table `projects`.
    	 * @param  boolean $id
    	 * @param  boolean $transactionId
    	 * @return string
    	 */
    	public function selectProject($id = false, $transactionId = false)
    	{
    		$fields = [
    			'P.id'
    		];

    		$initQuery = $this->select($fields)
    						  ->from('projects P')
    						  ->where(['P.status' => 1]);

    		$initQuery = ($transactionId) ? $initQuery->andWhere(['P.transaction_id' => ':transaction_id']) : $initQuery;

    		return $initQuery;
    	}
    }