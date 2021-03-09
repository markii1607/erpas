<?php
    namespace App\Model\WeatherReport;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class WeatherReportQueryHandler extends QueryHandler {

        public function selectWeatherReports($id = false)
        {
            $fields = array(
                'WR.id',
                'WR.project_id',
                'DATE_FORMAT(WR.date_of_report, "%M %d, %Y") as date_of_report',
                'WR.date_of_report as report_date',
            );

            $initQuery = $this->select($fields)
                              ->from('weather_reports WR')
                              ->where(array('WR.is_active' => ':is_active'));

            $initQuery = ($id) ? $initQuery->andWhere(array('WR.id' => ':id')) : $initQuery;

            return $initQuery;
        }

        public function selectWeatherReportItems($weather_report_id = false)
        {
            $fields = array(
                'WRI.id',
                'WRI.weather_report_id',
                'DATE_FORMAT(WRI.time_from, "%h:%i %p") as time_from',
                'DATE_FORMAT(WRI.time_to, "%h:%i %p") as time_to',
                'WRI.weather',
                'WRI.status',
                '"saved" as data_status'
            );

            $initQuery = $this->select($fields)
                              ->from('weather_report_items WRI')
                              ->where(array('WRI.is_active' => ':is_active'));

            $initQuery = ($weather_report_id) ? $initQuery->andWhere(array('WRI.weather_report_id' => ':weather_report_id')) : $initQuery;

            return $initQuery;
        }

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

        public function insertWeatherReport($data = array())
        {
            $initQuery = $this->insert('weather_reports', $data);

            return $initQuery;
        }

        public function insertWeatherReportItems($data = array())
        {
            $initQuery = $this->insert('weather_report_items', $data);

            return $initQuery;
        }

        public function updateWeatherReport($id = '', $data = array())
        {
            $initQuery = $this->update('weather_reports', $id, $data);

            return $initQuery;
        }

        public function updateWeatherReportItems($id = '', $data = array())
        {
            $initQuery = $this->update('weather_report_items', $id, $data);

            return $initQuery;
        }

    }
