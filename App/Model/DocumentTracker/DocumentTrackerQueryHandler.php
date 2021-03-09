<?php 
    namespace App\Model\DocumentTracker;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DocumentTrackerQueryHandler extends QueryHandler { 

        /**
         * `insertDocumentTracking` Query string that will insert to table `document_trackings`
         * @return string
         */
        public function insertDocumentTracking($data = [])
        {
            $initQuery = $this->insert('document_trackings', $data);

            return $initQuery;
        }

        /**
         * `insertTrackingDetail` Query string that will insert to table `tracking_details`
         * @return string
         */
        public function insertTrackingDetail($data = [])
        {
            $initQuery = $this->insert('tracking_details', $data);

            return $initQuery;
        }
    }