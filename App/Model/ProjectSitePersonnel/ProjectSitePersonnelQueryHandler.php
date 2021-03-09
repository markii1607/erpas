<?php 
    namespace App\Model\ProjectSitePersonnel;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ProjectSitePersonnelQueryHandler extends QueryHandler { 

        /**
         * `selectProjects` Query string that will select from table `projects`
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code',
                'P.name',
                'P.location'

            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

            return $initQuery;
        }


        /**
         * `insertLoans` Query string that will insert to table `loans`
         * @return string
         */
        public function insertLoans($data = [])
        {
            $initQuery = $this->insert('loans', $data);

            return $initQuery;
        }

        /**
         * `updateLoans` Query string that will update specific department information from table `loans`
         * @return string
         */
        public function updateLoans($id = '', $data = [])
        {
            $initQuery = $this->update('loans', $id, $data);

            return $initQuery;
        }
    }