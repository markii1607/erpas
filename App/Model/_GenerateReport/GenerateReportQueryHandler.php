<?php 
    namespace App\Model\GenerateReport;

    // local
    include_once "..\..\AbstractClass\QueryHandler.php";

    // server
    // include_once "../../AbstractClass/QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class GenerateReportQueryHandler extends QueryHandler {

        /**
         * `selectDocuments` Query string that will fetch from table `documents`
         * @param  boolean $id
         * @param  boolean $uniqueType
         * @return string
         */
    	public function selectDocuments($id = false, $uniqueType = false)
    	{
    		$fields = [
    			'D.id',
                'D.document_type_id',
                'DT.name as document_type_name'
    		];

    		$initQuery = $this->select($fields)
    						  ->from('documents D')
                              ->join(['document_types DT' => 'D.document_type_id = DT.id']);

            $initQuery = ($uniqueType) ? $initQuery->groupBy('D.document_type_id') : $initQuery;

    		return $initQuery;
    	}

        /**
         * `selectOutgoingDocuments` Query string that will fetch from table `outgoin_documents`
         * @param  boolean $id
         * @return string
         */
        public function selectOutgoingDocuments($id = false, $reportDate = false)
        {
            $fields = [
                // 'OD.id',
                'D.document_no',
                'D.description',
                'DT.name as document_type_name',
                'CONCAT(ES.fname," ",ES.mname," ",ES.lname) AS sender',
                // 'PS.name as ps_position_name',
                // 'DS.name as ps_department_name',
                'CONCAT(ER.fname," ",ER.mname," ",ER.lname) AS receiver',
                // 'PR.name as pr_position_name',
                // 'DR.name as pr_department_name',
                'OD.seen_status',
                'DATE_FORMAT(OD.date_sended, "%m/%d/%Y") as date_sended',
                'DATE_FORMAT(OD.date_received, "%m/%d/%Y") as date_received',
                'DATE_FORMAT(OD.date_seen, "%m/%d/%Y") as date_seen'
            ];

            $joins = [
                'documents D'       => 'D.id = OD.document_id',
                'document_types DT' => 'DT.id = D.document_type_id',
                'employees ES'      => 'OD.sender = ES.id',
                'employees ER'      => 'ER.id = OD.receiver',
                'positions PS'      => 'PS.id = ES.position_id',
                'positions PR'      => 'ER.position_id = PR.id',
                'departments DS'    => 'DS.id = ES.department_id',
                'departments DR'    => 'ER.department_id = DR.id'
            ];

            $rangeConditions = [
                ':start_date',
                ':end_date'
            ];

            $initQuery = $this->select($fields)
                              ->from('outgoing_documents OD')
                              ->join($joins);

            $initQuery = ($reportDate) ? $initQuery->whereRange('date_sended', $rangeConditions) : $initQuery;

            return $initQuery->orderBy('DT.name', 'asc');
        }
    }