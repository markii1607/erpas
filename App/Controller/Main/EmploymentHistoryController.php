<?php
    namespace App\Controller\Main;

    require_once("UpdateProfilesController.php");

    use App\Controller\Main\UpdateProfilesController as SubModuleController;

    class EmploymentHistoryController extends SubModuleController {

        /**
         * `getDetails` Fetching of first needed details.
         * @return multi-dimesional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'positions'   => $this->getCustomPositions(),
                'departments' => $this->getCustomDepartments()
            ];

            return $output;
        }

        /**
         * `getCustomDepartments` Fetching of departments without limit.
         * @return array
         */
        public function getCustomDepartments()
        {
			$departments = $this->dbCon->prepare($this->queryHandler->selectDepartments()->end());
            $departments->execute();

            return $departments->fetchAll(\PDO::FETCH_ASSOC);
        }

		/**
         * `getCustomPositions` Fetching of positions without limit.
         * @return array
         */
        public function getCustomPositions()
        {
			$positions = $this->dbCon->prepare($this->queryHandler->selectPositions()->end());
            $positions->execute();

            return $positions->fetchAll(\PDO::FETCH_ASSOC);
        }
    }