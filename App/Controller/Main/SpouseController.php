<?php
    namespace App\Controller\Main;

    require_once("UpdateProfilesController.php");

    use App\Controller\Main\UpdateProfilesController as SubModuleController;

    class SpouseController extends SubModuleController {

        /**
         * `getDetails` Fetching of first needed details.
         * @return multi-dimesional array
         */
        public function getDetails($data = [])
        {
            $output = [
                'attainments' => $this->getAttainments(),
                'schools'     => $this->getSchools(),
                'courses'     => $this->getCourses()
            ];

            return $output;
        }

        /**
         * `getAttainments` Fetching of attainments.
         * @return multi-dimensional array
         */
        public function getAttainments()
        {
            $attainments = $this->dbCon->prepare($this->queryHandler->selectAttainmentLevels()->end());
            $attainments->execute();

            return $attainments->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getSchools` Fetching of schools.
         * @return multi-dimensional array
         */
        public function getSchools()
        {
            $schools = $this->dbCon->prepare($this->queryHandler->selectSchools()->end());
            $schools->execute();

            return $schools->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `getCourses` Fetching of all courses.
         * @return multi-dimensional array
         */
        public function getCourses()
        {
            $courses = $this->dbCon->prepare($this->queryHandler->selectCourses()->end());
            $courses->execute();

            return $courses->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * `saveSpouseDetails` Save new `attainment_levels`, `schools` and `courses`.
         * @param object $input
         * @return  void
         */
        public function saveSpouseDetails($input)
        {
            $return = [
                'status' => true
            ];

            if (is_null($input->attainment->id)) {
                $attainmentData = [
                    'name'       => $input->attainment->name,
                    'created_by' => $_SESSION['user_id'],
                    'updated_by' => $_SESSION['user_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $saveAttainment = $this->dbCon->prepare($this->queryHandler->insertAttainmentLevel($attainmentData));
                $saveAttainment->execute($attainmentData);

                $attainmentLevelId = $this->dbCon->lastInsertId();

                $this->systemLogs($attainmentLevelId, 'attainment_levels', 'educational_background', 'add');

                $return['attainment_id'] = $attainmentLevelId;
            }

            if (is_null($input->school->id)) {
                $schoolData = [
                    'name'       => $input->school->name,
                    'created_by' => $_SESSION['user_id'],
                    'updated_by' => $_SESSION['user_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $saveSchool = $this->dbCon->prepare($this->queryHandler->insertSchool($schoolData));
                $saveSchool->execute($schoolData);

                $schoolId = $this->dbCon->lastInsertId();

                $this->systemLogs($schoolId, 'schools', 'educational_background', 'add');
                
                $return['school_id'] = $schoolId;
            }

            if (is_null($input->course->id)) {
                $courseData = [
                    'name'       => $input->course->name,
                    'created_by' => $_SESSION['user_id'],
                    'updated_by' => $_SESSION['user_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $saveCourse = $this->dbCon->prepare($this->queryHandler->insertCourse($courseData));
                $saveCourse->execute($courseData);

                $courseId = $this->dbCon->lastInsertId();

                $this->systemLogs($courseId, 'courses', 'educational_background', 'add');
                
                $return['course_id'] = $courseId;
            }

            return $return;
        }
    }