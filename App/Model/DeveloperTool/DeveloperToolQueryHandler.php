<?php 
    namespace App\Model\DeveloperTool;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DeveloperToolQueryHandler extends QueryHandler { 
        /** 
         * `archiveTransactionApproval` Query string that will update to table `DeveloperTools`
         * @param  string $id
         * @param  array  $data
         * @return string
         */
        public function selectDeveloperFunctions()
        {
            $fields = [
                'DF.id',
                'DF.function_name',
                'DF.function_description',
                'DF.development',
                'DF.created_by',
                'DF.created_at',  
            ];

            $initQuery = $this->select($fields)
                              ->from('developer_functions DF')
                              ->where(['DF.is_active' => ':is_active']);
                              
            return $initQuery;
        }

        public function insertDeveloperFunction($data = array())
        {
          $initQuery = $this->insert('developer_functions', $data);
          return $initQuery;
        }

    }