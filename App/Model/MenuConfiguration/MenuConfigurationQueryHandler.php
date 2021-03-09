<?php 
    namespace App\Model\MenuConfiguration;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class MenuConfigurationQueryHandler extends QueryHandler { 

        /**
         * `selectMenus` Query string that will fetch menu.
         * @return string
         */
        public function selectMenus($id = false, $parent = false, $name = false, $office = false)
        {
            $fields = [
                'M.id',
                'M.name',
                'M.level',
                'M.icon',
                'M.box_color',
                'M.link',
                'M.office'
            ];

            $initQuery = $this->select($fields)
                              ->from('menus M')
                              ->where(['M.is_active' => ':is_active', 'M.system' => 2]);

            $initQuery = ($id) 	   ? $initQuery->andWhere(['M.id' => ':id']) : $initQuery;
            $initQuery = ($parent) ? $initQuery->andWhereNull(['M.parent'])  : $initQuery->andWhere(['M.parent' => ':parent']);
            $initQuery = ($name)   ? $initQuery->andWhereLike(['M.name' => ':name']) : $initQuery;
            $initQuery = ($office) ? $initQuery->andWhereLike(['M.office' => ':office']) : $initQuery;

            $initQuery = $initQuery->orderBy('M.id', 'asc');

            return $initQuery;
        }

        /**
         * `insertMenu` Query string that will insert to table `menus`
         * @return string
         */
        public function insertMenu($data = [])
        {
            $initQuery = $this->insert('menus', $data);

            return $initQuery;
        }

        /**
         * `updateMenu` Query string that will update specific department information from table `menus`
         * @return string
         */
        public function updateMenu($id = '', $data = [])
        {
            $initQuery = $this->update('menus', $id, $data);

            return $initQuery;
        }
    }