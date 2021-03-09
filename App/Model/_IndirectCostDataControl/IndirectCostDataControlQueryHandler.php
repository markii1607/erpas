<?php 
    namespace App\Model\IndirectCostDataControl;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class IndirectCostDataControlQueryHandler extends QueryHandler {
        /**
         * `selectIndirectCostDescriptions` Query string that will select from table `indirect_cost_descriptions`.
         * @param  boolean $id
         * @param  boolean $name
         * @return string
         */
        public function selectIndirectCostDescriptions($id = false, $name = false)
        {
            $fields = [
                'ICD.id',
                'ICD.name'
            ];

            $initQuery = $this->select($fields)
                              ->from('indirect_cost_descriptions ICD')
                              ->where(['ICD.status' => ':status']);

            $initQuery = ($id)   ? $initQuery->andWhere(['ICD.id' => ':id'])     : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhere(['ICD.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertIndirectCostDescription` Query string that will insert to table `indirect_cost_descriptions`
         * @return string
         */
        public function insertIndirectCostDescription($data = [])
        {
            $initQuery = $this->insert('indirect_cost_descriptions', $data);

            return $initQuery;
        }
    }