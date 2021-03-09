<?php 
    namespace App\Model\UploadHrisDatas;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class UploadHrisDatasQueryHandler extends QueryHandler { 

        /**
         * `selectProjects` Query string that will select from table `projects`.
         * @param  boolean $id`
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = array(
                'P.id',
                'P.project_code',
                'P.name',
                'P.location',
                'P.longitude',
                'P.latitude',
                'P.is_on_going',
                'P.created_by',
                'P.updated_by',
                'P.created_at',
                'P.updated_at',
                'CONCAT(CBPI.lname, " ",CBPI.fname, " ", CBPI.mname) as created_by_name',
                'CONCAT(UBPI.lname, " ",UBPI.fname, " ", UBPI.mname) as updated_by_name'
            );

            $leftJoins = array(
                'users CBU'                  => 'P.created_by = CBU.id',
                'personal_informations CBPI' => 'CBU.personal_information_id = CBPI.id',
                'users UBU'                  => 'P.updated_by = UBU.id',
                'personal_informations UBPI' => 'UBU.personal_information_id = UBPI.id'
            );

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->leftJoin($leftJoins)
                              ->where(array('P.status' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

            return $initQuery;
        }
    }