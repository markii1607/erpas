<?php 
    namespace App\Model\Selector;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class ProjectQueryHandler extends QueryHandler { 
        /**
         * `selectProjects` Query string that will select from table `projects`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjects($id = false, $boqApproval = false)
        {
            $fields = [
                'P.id',
                'P.name',
                'P.project_code',
                'P.location',
                'DATE_FORMAT(P.updated_at, "%c/%e/%Y %l:%i %p") as date_revised',
                'CONCAT(E.fname, " ", E.mname, " ", E.lname) as project_manager_name',
            ];

            $joins = [
               'employees E' => 'E.id = P.project_manager'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->join($joins)
                              ->where(['P.status' => ':status']);

            $initQuery = ($id)          ? $initQuery->andWhere(['P.id' => ':id'])                     : $initQuery;
            $initQuery = ($boqApproval) ? $initQuery->andWhere(['P.boq_approval' => ':boq_approval']) : $initQuery;

            return $initQuery;
        }
    }