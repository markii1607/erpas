<?php 
    namespace App\AbstractClass;

    class QueryHandler {
        /**
         * `$query` Query Compiler.
         * @var protected class
         */
        protected $query;

        /**
         * `select` Global usage of `select` clause
         * @param  array  $fields 
         *         format : [
         *              'field1',
         *              'field2'
         *         ]
         * @return object $this
         */
        public function select($fields = [])
        {
            $count = count($fields);
            $string = "SELECT ";

            foreach ($fields as $key => $field) {
                if ($count - 1 == $key) {
                    $string = $string.$field.' ';
                } else {
                    $string = $string.$field.', ';
                }
            }

            $this->query = $string; 

            return $this;
        }

        /**
         * `from` Global usage of `from` clause.
         * @param  string $table
         * @return object $this
         */
        public function from($table = '')
        {
            $string = "FROM ";

            $this->query = $this->query.$string.$table.' ';

            return $this;
        }

        /**
         * `join` Global usage of `join` clause.
         * @param  array  $tables
         * @return object $this
         */
        public function join($tables = [])
        {
            $count = count($tables);
            $string = "";

            foreach ($tables as $key => $value) {
                $string = $string.'JOIN '.$key.' ON '.$value.' ';
            }

            $this->query = $this->query.$string;

            return $this;
        }

        /**
         * `leftJoin` Global usage of `left join` clause.
         * @param  array  $tables
         * @return object $this
         */
        public function leftJoin($tables = [])
        {
            $count = count($tables);
            $string = "";

            foreach ($tables as $key => $value) {
                $string = $string.'LEFT JOIN '.$key.' ON '.$value.' ';
            }

            $this->query = $this->query.$string;

            return $this;
        }

        /**
         * `where` Global usage of `where` clause.
         * @param  array  $conditions 
         *         format : [
         *             'field1' => 0
         *             'field2' => true
         *         ]
         *                
         * @return object $this
         */
        public function where($conditions = [])
        {
            $count      = count($conditions);
            $keyCounter = 0;
            $string     = "WHERE ";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $string = $string.$key.' = '.$condition.' ';
                } else {
                    $string = $string.$key.' = '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string;

            return $this;
        }

        /**
         * `andWhere` special usage of where clause.
         * @param  array  $conditions
         * @return object
         */
        public function andWhere($conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = " AND ";
            $andConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andConditions = $andConditions.$key.' = '.$condition.' ';
                } else {
                    $andConditions = $andConditions.$key.' = '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.$andConditions;

            return $this;
        }

        /**
         * `andWhereNotIn` Global usage of `and where not in` clause condition.
         * @param  string $field
         * @param  array  $conditions
         * @return object
         */
        public function andWhereIn($field = '', $conditions = array())
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ".$field." IN ";
            $notInConditions = "";


            foreach ($conditions as $key => $value) {
                if ($count - 1 == $keyCounter) {
                    $notInConditions = $notInConditions.$value;
                } else {
                    $notInConditions = $notInConditions.$value.', ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.'('.$notInConditions.')';

            return $this;
        }
        
        /**
         * `whereOr` Global usage of where or condition.
         * @param  array  $conditions
         * @return string
         */
        public function whereOrOneField($field = '', $conditions = [])
        {
            $count      = count($conditions);
            $keyCounter = 0;
            $string     = "WHERE ";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $string = $string.$field.' = '.$condition.' ';
                } else {
                    $string = $string.$field.' = '.$condition.' OR ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string;

            return $this;
        }

        /**
         * `whereAndOrOneField` Global usage of unique where condition.
         * @param  array  $conditions
         * @return string
         */
        public function whereAndOrOneField($field = '', $conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ";
            $andOrConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andOrConditions = $andOrConditions.$field.' = '.$condition.' ';
                } else {
                    $andOrConditions = $andOrConditions.$field.' = '.$condition.' OR ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.'('.$andOrConditions.')';

            return $this;
        }

        /**
         * `whereAndOr` special usage of where clause.
         * @param  array  $conditions
         * @return object
         */
        public function whereAndOr($conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ";
            $andOrConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andOrConditions = $andOrConditions.$key.' = '.$condition.' ';
                } else {
                    $andOrConditions = $andOrConditions.$key.' = '.$condition.' OR ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.'('.$andOrConditions.')';

            return $this;
        }

        /**
         * `whereLike` Global usage of `where like` clause condition.
         * @param  array  $conditions
         * @return object
         */
        public function whereLike($conditions = [])
        {
            $count      = count($conditions);
            $keyCounter = 0;
            $string     = "WHERE ";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $string = $string.$key.' LIKE '.$condition.' ';
                } else {
                    $string = $string.$key.' LIKE '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string;

            return $this;
        }

        /**
         * `orWhereLike` Global usage of `or like` clause condition.
         * @param  array  $conditions
         * @return object
         */
        public function orWhereLike($conditions = array())
        {
            $count      = count($conditions);
            $keyCounter = 0;
            $string     = "(";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $string = $string.$key.' LIKE '.$condition.') ';
                } else {
                    $string = $string.$key.' LIKE '.$condition.' OR ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string;

            return $this;
        }
        
        /**
         * `andWhereLike` Global usage of `and where like` clause condition.
         * @param  array  $conditions
         * @return object
         */
        public function andWhereLike($conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ";
            $andConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andConditions = $andConditions.$key.' LIKE '.$condition.' ';
                } else {
                    $andConditions = $andConditions.$key.' LIKE '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.$andConditions;

            return $this;
        }

        /**
         * `whereNotIn` Global usage of `where not in` clause condition.
         * @param  string $field
         * @param  array  $conditions
         * @return object
         */
        public function whereNotIn($field = '', $conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "WHERE ".$field." NOT IN ";
            $notInConditions = "";


            foreach ($conditions as $key => $value) {
                if ($count - 1 == $keyCounter) {
                    $notInConditions = $notInConditions.$value;
                } else {
                    $notInConditions = $notInConditions.$value.', ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.'('.$notInConditions.')';

            return $this;
        }

        /**
         * `andWhereNot` Global usage of `and where not` clause condition.
         * @param  array  $conditions
         * @return object
         */
        public function andWhereNot($conditions = array())
        {
            $count          = count($conditions);
            $keyCounter     = 0;
            $string         = "AND ";
            $andConditions  = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andConditions = $andConditions.$key.' <> '.$condition.' ';
                } else {
                    $andConditions = $andConditions.$key.' <> '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.$andConditions;

            return $this;
        }

        /**
         * `andWhereNotIn` Global usage of `and where not in` clause condition.
         * @param  string $field
         * @param  array  $conditions
         * @return object
         */
        public function andWhereNotIn($field = '', $conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ".$field." NOT IN ";
            $notInConditions = "";


            foreach ($conditions as $key => $value) {
                if ($count - 1 == $keyCounter) {
                    $notInConditions = $notInConditions.$value;
                } else {
                    $notInConditions = $notInConditions.$value.', ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.'('.$notInConditions.')';

            return $this;
        }

        /**
         * `andWhereNull` Global usage of `and is null` clause condition.
         * @param  array  $conditions
         * @return object
         */
        public function andWhereNull($conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ";
            $andConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andConditions = $andConditions.$condition.' IS NULL ';
                } else {
                    $andConditions = $andConditions.$condition.' IS NULL AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.$andConditions;

            return $this;
        }

        /**
         * `andWhereNotNull` Global usage of `and is not like` clause condition.
         * @param  array  $conditions
         * @return object
         */
        public function andWhereNotNull($conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = "AND ";
            $andConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andConditions = $andConditions.$condition.' IS NOT NULL ';
                } else {
                    $andConditions = $andConditions.$condition.' IS NOT NULL AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.$andConditions;

            return $this;
        }

        /**
         * `andWhereNotEqual` special usage of `and where not equal` clause.
         * @param  array  $conditions
         * @return object
         */
        public function andWhereNotEqual($conditions = [])
        {
            $count           = count($conditions);
            $keyCounter      = 0;
            $string          = " AND ";
            $andConditions = "";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $andConditions = $andConditions.$key.' <> '.$condition.' ';
                } else {
                    $andConditions = $andConditions.$key.' <> '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.$andConditions;

            return $this;
        }

        /**
         * `andWhereRange` Custom where | range usage
         * @param  array  $conditions
         * @return object
         */
        public function andWhereRange($field, $conditions = array())
        {
            $count      = count($conditions);
            $keyCounter = 0;
            $string     = "AND (";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $string = $string.$field.' <= '.$condition.' ';
                } else {
                    $string = $string.$field.' >= '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string.')';

            return $this;
        }

        /**
         * `whereNotEqual` Global usage of `where not equal` clause.
         * @param  array  $conditions 
         *         format : [
         *             'field1' => 0
         *             'field2' => true
         *         ]
         *                
         * @return object $this
         */
        public function whereNotEqual($conditions = [])
        {
            $count      = count($conditions);
            $keyCounter = 0;
            $string     = "WHERE ";

            foreach ($conditions as $key => $condition) {
                if ($count - 1 == $keyCounter) {
                    $string = $string.$key.' <> '.$condition.' ';
                } else {
                    $string = $string.$key.' <> '.$condition.' AND ';
                }

                $keyCounter++;
            }

            $this->query = $this->query.$string;

            return $this;
        }

        /**
         * `orderBy` Global usage of `order by` clause
         * @param  string $field
         * @param  string $order
         * @return object $this
         */
        public function orderBy($field = '', $order = '')
        {
            $string = "ORDER BY ";

            $this->query = $this->query.$string.$field.' '.$order.' ';

            return $this;
        }

        /**
         * `groupBy` Global usage of `group by` clause.
         * @param  string $field
         * @return object $this
         */
        public function groupBy($field = '')
        {
            $string = "GROUP BY ";

            $this->query = $this->query.$string.$field.' ';

            return $this;
        }

        /**
         * `limit` Global usage of `group by` clause.
         * @param  int $field
         * @return object $this
         */
        public function limit($count = 1)
        {
            $string = "LIMIT ";

            $this->query = $this->query.$string.$count.' ';

            return $this;
        }

        /**
         * `logicEx` Adding of logical expression.
         * @param  string $logic
         * @return string
         */
        public function logicEx($logic)
        {
            $this->query = $this->query.$logic.' ';

            return $this;
        }
        
        /**
         * `insert` Global usage of `insert` Clause.
         * @param  string $table
         * @param  array  $inputs
         * @return string
         */
        public function insert($table = '', $inputs = [])
        {
            $string     = "INSERT INTO ".$table;
            $count      = count($inputs);
            $keyCounter = 0;
            $fields     = "";
            $values     = "";

            foreach ($inputs as $key => $value) {
                if ($count - 1 == $keyCounter) {
                    $fields = $fields.$key;
                    $values = $values.':'.$key;
                } else {
                    $fields = $fields.$key.', ';
                    $values = $values.':'.$key.', ';
                }

                $keyCounter++;
            }

            $this->query = $string.' ('.$fields.') VALUES ('.$values.')';

            return $this->query;
        }

        /**
         * `update` Global usage of `update` clause.
         * @param  string $table
         * @param  string $id
         * @param  array  $inputs
         * @return string
         */
        public function update($table = '', $id = '', $inputs = [], $foreignKey = '', $fkValue = '')
        {
            $string       = "UPDATE ".$table." SET";
            $count        = count($inputs);
            $keyCounter   = 0;
            $updateFields = "";

            foreach ($inputs as $key => $value) {
                if ($count - 1 == $keyCounter) {
                    $updateFields = $updateFields.' '.$key.' = :'.$key;
                } else {
                    $updateFields = $updateFields.' '.$key.' = :'.$key.', ';
                }

                $keyCounter++;
            }

            $this->query = ($id != '')         ? $string.$updateFields." WHERE id = ".$id : ''; // temp fix, id must not be empty otherwise all records will be updated
            $this->query = ($foreignKey != '') ? $string.$updateFields." WHERE ".$foreignKey." = ".$fkValue : $this->query;

            return $this->query;
        }

        /**
         * `delete` Global usage of `delete` clause.
         * @param  string $table
         * @param  array  $input
         * @return object
         */
        public function delete($table = '')
        {
            $this->query = "DELETE FROM ".$table.' ';

            return $this;
        }

        /**
         * `end` End of query string
         * @return string
         */
        public function end()
        {
            return $this->query;
        }

        /**
         * `systemLogs` Logs all the action taken in this module.
         * @return string
         */
        public function systemLogs($data)
        {
            $initQuery = $this->insert('system_logs', $data);

            return $initQuery;
        }

        /**
        * `selectSessionLogs` Query String that will select existing entry of IP loggen in table `session_logs`.
        * @return string
        */
        public function selectSessionLogs($hasIp = false)
        {
            $fields = [
                'SL.id',
                'SL.ip_address',
                'SL.user_id',
                'SL.session_data',
                'SL.status',
            ];

            $initQuery = $this->select($fields)
                              ->from('session_logs SL');

            $initQuery = ($hasIp) ? $initQuery->Where(array('SL.ip_address' => ':ip_address')) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectUserName` Query String that will select username from table `users`.
         * @return string
         */
        public function selectUserName($user_id = false)
        {
            $fields = [
                'U.id',
                'U.username',
            ];

            $initQuery = $this->select($fields)
                              ->from('users U');

            $initQuery = ($user_id) ? $initQuery->Where(array('U.id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * `insertSessionLogs` Query String that will insert Login Data to table `session_logs`.
         * @return string
         */
        public function insertSessionLogs($data = array())
        {
            $initQuery = $this->insert('session_logs', $data);
            return $initQuery;
        }

        /**
         * `updateSessionLogs` Query String that will update Login Data to table `session_logs`.
         * @return string
         */
        public function updateSessionLogs($id = '', $data = [])
        {
            $initQuery = $this->update('session_logs', $id, $data);
    
            return $initQuery;
        }
    }