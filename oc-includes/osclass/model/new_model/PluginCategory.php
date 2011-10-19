<?php

    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    /**
     * 
     */
    class PluginCategory extends DAO
    {
        /**
         *
         * @var type 
         */
        private static $instance ;

        /**
         *
         * @return type 
         */
        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * 
         */
        public function __construct()
        {
            parent::__construct() ;
            $this->setTableName('t_plugin_category') ;
            /* $this->setPrimaryKey('pk_i_id') ; */
            $this->setFields( array('s_plugin_name', 'fk_i_category_id') ) ;
        }

        /**
         *
         * @param type $categoryId
         * @return type 
         */
        function findByCategoryId($categoryId)
        {
            $this->dao->select( $this->getFields() ) ;
            $this->dao->from( $this->getTableName() ) ;
            $this->dao->where('fk_i_category_id', $categoryId) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            return $result->result() ;
        }

        /**
         *
         * @param type $plugin
         * @return type 
         */
        function listSelected($plugin)
        {
            $this->dao->select( $this->getFields() ) ;
            $this->dao->from( $this->getTableName() ) ;
            $this->dao->where('s_plugin_name', $plugin) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return array() ;
            }

            $list = array() ;
            foreach($result->result() as $sel) {
                $list[] = $sel['fk_i_category_id'] ;
            }

            return $list;
        }

        /**
         *
         * @param type $categoryName
         * @param type $categoryId
         * @return type 
         */
        function isThisCategory($categoryName, $categoryId)
        {
            $this->dao->select('COUNT(*) AS numrows') ;
            $this->dao->from( $this->getTableName() ) ;
            $this->dao->where('fk_i_category_id', $categoryId) ;
            $this->dao->where('s_plugin_name', $categoryName) ;

            $result = $this->dao->get() ;

            if( $result == false ) {
                return false ;
            }

            if( $result->numRows() == 0 ) {
                return false ;
            }

            $row = $result->row() ;

            if( $row['numrows'] == 0 ) {
                return false ;
            }

            return true ;
        }

    }

    /* file end: ./oc-includes/osclass/model/new_model/PluginCategory.php */
?>