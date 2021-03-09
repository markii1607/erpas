<?php 
    namespace App\Model\Dashboard;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class DashboardQueryHandler extends QueryHandler {

        /**
         * `selectMenus` Fetching of parent menus of specific user.
         * @param  boolean $id
         * @param  boolean $userId
         * @return string
         */
        public function selectMenus($id = false, $userId = false, $parent = false)
        {
            $fields = [
                'DISTINCT(M.id) as id',
                'M.name',
                'M.level',
                'M.icon',
                'M.box_color',
                'M.link',
                'M.office'
            ];

            $initQuery = $this->select($fields)
                              ->from('menus M')
                              ->join(['user_accesses UA' => 'M.id = UA.menu_id'])
                              ->where(['UA.is_active' => ':is_active', 'M.is_active' => ':is_active', 'M.system' => 2]);

            $initQuery = ($userId) ? $initQuery->andWhere(['UA.user_id' => ':user_id']) : $initQuery;
            $initQuery = ($parent) ? $initQuery->andWhereNull(['M.parent']) : $initQuery->andWhere(['M.parent' => ':parent']);

            $initQuery = $initQuery->orderBy('M.id', 'asc');

            return $initQuery;
        }
    }