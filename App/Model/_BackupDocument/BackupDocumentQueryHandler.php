<?php 
    namespace App\Model\BackupDocument;

    include_once "..\..\AbstractClass\QueryHandler.php";

    use App\AbstractClass\QueryHandler;

    class BackupDocumentQueryHandler extends QueryHandler { 
        /**
         * `selectBackupMmmFiles` Query string that will select backup file information from table `backup_mmm_file`.
         * @return string
         */
        public function selectBackupMmmFiles($id = false, $userId = false)
        {
            $fields = [
                'BMF.id',
                'BMF.description',
                'DATE_FORMAT(BMF.created_at, "%m/%d/%Y") as backup_date'
            ];

            $conditions = [
                'BMF.status' => 1,
            ];
          
            ($id)     ? $conditions['BMF.id']      = ':id'      : '';
            ($userId) ? $conditions['BMF.user_id'] = ':user_id' : '';

            $initQuery = $this->select($fields)
                              ->from('backup_mmm_files BMF')
                              ->where($conditions)
                              ->orderBy('BMF.created_at', 'desc');

            return $initQuery;
        }

        /**
         * `selectMmmFiles` Query string that will select from table `mmm_files`.
         * @param  boolean $id
         * @param  boolean $prijectId
         * @return string
         */
        public function selectMmmFiles($id = false, $backupMmmFileId = false)
        {
            $fields = [
                'MF.id',
                'MF.file'
            ];

            $conditions = [];

            ($id)              ? $conditions['MF.id']                 = ':id'                 : '';
            ($backupMmmFileId) ? $conditions['MF.backup_mmm_file_id'] = ':backup_mmm_file_id' : '';

            $initQuery = $this->select($fields)
                              ->from('mmm_files MF')
                              ->where($conditions);

            return $initQuery;
        }

        /**
         * `insertBackupMmmFile` Query string that will insert to table `backup_mmm_files`
         * @return string
         */
        public function insertBackupMmmFile($data = [])
        {
            $initQuery = $this->insert('backup_mmm_files', $data);

            return $initQuery;
        }

        /**
         * `insertMmmFile` Query string that will insert to table `mmm_files`
         * @return string
         */
        public function insertMmmFile($data = [])
        {
            $initQuery = $this->insert('mmm_files', $data);

            return $initQuery;
        }

        /**
         * `softDeleteFile` Query string that will update specific backup mmm file's status from `backup_mmm_files` table.
         * @return string
         */
        public function softDeleteFile($id = '', $data = [])
        {
            $initQuery = $this->update('backup_mmm_files', $id, $data);

            return $initQuery;
        }
    }