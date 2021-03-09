<?php
    namespace App\Model\MakeNewRequest;

    require_once('MakeNewRequestQueryHandler.php');

    use App\Model\MakeNewRequest\MakeNewRequestQueryHandler;

    class PrsManpowerServicesQueryHandler extends MakeNewRequestQueryHandler {

        /**
         * `selectRequestTypes` Query string that will select from table `request_types`.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectRequestTypes($id = false, $userId = false)
        {
            $fields = array(
                'RT.id',
                'RT.name',
                'RT.cost_code',
                'RT.updated_by',
                'RT.updated_at',
                'RT.status'
            );

            $initQuery = $this->select($fields)
                              ->from('request_types RT')
                              ->where(array('RT.status' => ':status'));

            $initQuery = ($id) ? $initQuery->andWhere(array('RT.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjects` Query string that will select from table `projects`.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectProjects($id = false, $userId = false)
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name',
                'P.location',
                'P.longitude',
                'P.latitude',
                'P.is_on_going',
            );

            $joins = array(
              'p_wds PWDS' => 'PWDS.project_id = P.id',
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->join($joins)
                              ->where(array('P.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query string that will select from table `departments`.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectDepartments($id = false, $userId = false)
        {
            $fields = array(
                'D.id',
                'D.code',
                'D.charging',
                'D.name',
            );

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(array('D.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('D.id' => ':id')) : $initQuery;

            return $initQuery;
        }
    }
