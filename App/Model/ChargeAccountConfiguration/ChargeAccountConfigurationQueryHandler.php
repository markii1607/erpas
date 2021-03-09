<?php 
    namespace App\Model\ChargeAccountConfiguration;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class ChargeAccountConfigurationQueryHandler extends QueryHandler {

        /**
         * `selectChargeAccounts` Query string that will fetch from table `charge_accounts`.
         * @param  boolean $id
         * @return string
         */
        public function selectChargeAccounts($id = false)
        {
            $fields = [
                'CA.id',
                'IF(CA.project_id IS NULL, D.charging, P.project_code) as charge_account',
                'IF(CA.project_id IS NULL, D.name, P.name) as description',
                'CA.status',
            ];

            $leftJoins = [
                'projects P'    => 'P.id = CA.project_id',
                'departments D' => 'CA.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('charge_accounts CA')
                              ->leftJoin($leftJoins)
                              ->where(['CA.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['CA.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectProjects` Query string that will fetch from table `projects`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code as charging',
                'P.name',
                '"P" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query string that will fetch from table `departments`.
         * @param  boolean $id
         * @return string
         */
        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.charging',
                'D.name',
                '"D" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['D.id' => ':id']) : $initQuery;

            return $initQuery;
        }
        /**
         * `insertChargeAccount` Query string that will insert to table `charge_accounts`
         * @return string
         */
        public function insertChargeAccount($data = [])
        {
            $initQuery = $this->insert('charge_accounts', $data);

            return $initQuery;
        }

        /**
         * `updateChargeAccount` Query string that will update specific charge account information from table `charge_accounts`
         * @return string
         */
        public function updateChargeAccount($id = '', $data = [])
        {
            $initQuery = $this->update('charge_accounts', $id, $data);

            return $initQuery;
        }
    }