<?php


/*
 *      OSCLass - software for creating and publishing online classified
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



    // Class: BaseDAO_osclass_t_user_email_tmp
    // Table: t_user_email_tmp
    // Database: osclass

    abstract class BaseDAO_osclass_t_user_email_tmp extends DAO
    {
        /* Attributes */
        var $DO ;
        var $aDO ;

        var $lastid ;                // Attribute to save the new ID of an INSERT
        var $aLastPk ;
        var $noDuplicateFields ;     // Attribute that implements the no duplication of the value of a field that is not a PK
        var $noDuplicateUniqueKey ;
        
        /* Constructor */
        function __construct() {
            $this->getConnection(DB_NAME, DB_HOST, DB_USER, DB_PASSWORD, DEBUG_LEVEL, "osclass") ;
        }

        /* Destrutor */
        function __destruct() {
            unset($this->DO) ;
            unset($this->aDO) ;
            unset($this->lastid) ;
            unset($this->aLastPk) ;
            unset($this->noDuplicateFields) ;
            unset($this->noDuplicateUniqueKey) ;
        }

        /* Methods */


        ////////////////////
        // SELECT METHODS //
        ////////////////////

        // Method that selects a row of "t_user_email_tmp" using the PK
        function selectBy_pk($fk_i_user_id, $aCampos="", $where="")
        {
            $this->DO = "" ;
            $sql  = "SELECT " ;

            if ($aCampos == "") $sql .= "* " ;
            else $sql .= implode (", ", $aCampos) . " " ;

            $sql .= "FROM " . $this->get_table_name('t_user_email_tmp') . " " ;
            $sql .= "WHERE fk_i_user_id = '" . addslashes($fk_i_user_id) . "'" ;
            if ($where != "") $sql .= " AND " . $where ;

            //Recordset Object
            $rs = new DBRecordsetClass($this->conn, $sql) ;
            $rs->query() ;

            //Saving the results in DO
            if (!$rs->movenext())
            {
                $this->DO = $rs->fetch_array() ;
            }
         }
        // Method that selects all the rows of "t_user_email_tmp" that satisfies the WHERE conditions
        function selectAll($where = "", $order = "", $inicio = "", $total = "", $aCampos = "", $group = "")
        {
            $sql  = "SELECT " ;

            if ($aCampos == "") $sql .= "* " ;
            else $sql .= implode (", ", $aCampos) . " " ;

            $sql .= "FROM " . $this->get_table_name('t_user_email_tmp') . " " ;

            if ($where) $sql .= " WHERE " . $where ;
            if ($group) $sql .= " GROUP BY " . $group ;
            if ($order) $sql .= " ORDER BY " . $order ;
            if ($inicio !== "" && $total !== "") $sql .= " LIMIT " . $inicio . ", " . $total ;

            //Recordset object
            $rs = new DBRecordsetClass($this->conn, $sql) ;
            $rs->query() ;

            //We save results into aDO
            $this->aDO = array() ;
			
        	$is_set_pk_aCampos = ($aCampos == "" || (is_array($aCampos) && isset($aCampos["fk_i_user_id"])));
			
					
            while (!$rs->movenext())
            {
            	if($is_set_pk_aCampos) {
                	$this->aDO["pk_" . $rs->value("fk_i_user_id")] = $rs->fetch_array() ;
				} else {
                	$this->aDO[] = $rs->fetch_array() ;
				}
            }
        }


        // Method that return the total of the rows of "t_user_email_tmp" that satisfies the WHERE conditions
        function selectTotal($where = "", $group = "")
        {
            $sql  = "SELECT count(fk_i_user_id) AS total " ;
            if ($group) $sql.= ", " . $group . " " ;
            $sql .= "FROM " . $this->get_table_name('t_user_email_tmp') . " " ;
            if ($where) $sql .= " WHERE " . $where ;
            if ($group) $sql .= " GROUP BY " . $group ;

            //Recordset object
            $rs = new DBRecordsetClass($this->conn, $sql) ;
            if ($rs->query()) {
                if ($group) {
                    while (!$rs->movenext())
                    {
                        $this->aDO["gk_".$rs->value($group)] = $rs->value("total") ;
                    }
                    return $this->aDO ;
                } else {
                    $rs->movenext() ;
                    return ($rs->value("total")) ;
                }
            } else return (0);
        }

        ////////////////////
        // INSERT METHODS //
        ////////////////////
        
        function insert($DO)
        {
            $can_insert = true ;
            if (is_array($this->noDuplicateFields) || is_array($this->noDuplicateUniqueKey))
            {
                $where_select = "" ;
                if (is_array($this->noDuplicateFields)) {
                    foreach ($this->noDuplicateFields as $val) {
                        if ($where_select != "") $where_select .= " OR " ;
                        $where_select .= $val . " = '" . addslashes($DO[$val]) . "'" ;
                    }
                } else {
                    foreach ($this->noDuplicateUniqueKey as $val) {
                        if ($where_select != "") $where_select .= " AND " ;
                        $where_select .= $val . " = '" . addslashes($DO[$val]) . "'" ;
                    }
                }

                $can_insert = true ;
                if ($where_select != "") {
                    $sql = "SELECT * FROM " . $this->get_table_name('t_user_email_tmp') . " WHERE " . $where_select ;

                    //Recordset object
                    $rs = new DBRecordsetClass($this->conn, $sql) ;
                    $rs->query() ;

                    //If it return results we cannot do the insert
                    if (!$rs->movenext()) {
                        $can_insert = false ;

                        //Useful if we need to do the UPDATE, just in case the register exists
                        $this->aLastPk = array() ;
                        $this->aLastPk["fk_i_user_id"] = $rs->value("fk_i_user_id") ;
                    }
                }
            }

            if ($can_insert)
            {
                $sql  = "INSERT INTO " . $this->get_table_name('t_user_email_tmp') . " SET " ;

                $cond = "" ;
                if (isset($DO["fk_i_user_id"]))
                {
                    if ($cond != "") $cond .= ", " ;
                    if ($DO["fk_i_user_id"] !== "")     $cond .= "fk_i_user_id = " . addslashes($DO["fk_i_user_id"]) ;
                    else $cond .= "fk_i_user_id = NULL" ;
                }
                if (isset($DO["s_new_email"]))
                {
                    if ($cond != "") $cond .= ", " ;
                       $cond .= "s_new_email = '" . addslashes($DO["s_new_email"]) . "'" ;
                }
                if (isset($DO["dt_date"]))
                {
                    if ($cond != "") $cond .= ", " ;
                    if ($DO["dt_date"] !== "")    $cond .= "dt_date = '" . addslashes($DO["dt_date"]) . "'" ;
                    else $cond .= "dt_date = NULL" ;
                }
                $sql .= $cond ;

                //SQL Execution
                $comm = new DBCommandClass($this->conn, $sql) ;
                $status = $comm->execute() ;
                if (!$status && $this->conn->error_level == 1062) $status = -2 ;
                //Only for rows inserted with an auto_increment as a PK
                $this->lastid = $comm->lastid ;
                return($status) ;

            } else {
                return(-2) ;
            }
        }

		function multiple_inserts($mDO) {

			if(count($mDO) == 0) return;

            $sql  = "INSERT INTO " . $this->get_table_name('t_user_email_tmp') . " (fk_i_user_id,s_new_email,dt_date) VALUES " ;
            $cond = "" ;$primera = true;

						foreach($mDO as $DO) {
							$cond = "";
							if(!$primera) $sql .= ",";
							$sql .= " ( ";

			
		                if (isset($DO["fk_i_user_id"]))
		                {
		                    if ($cond != "") $cond .= ", " ;
		                    if ($DO["fk_i_user_id"] !== "")     $cond .= "" . addslashes($DO["fk_i_user_id"]) ;
		                    else $cond .= " NULL" ; }
		                if (isset($DO["s_new_email"]))
		                {
		                    if ($cond != "") $cond .= ", " ;
		                       $cond .= "'" . addslashes($DO["s_new_email"]) . "'" ; }
		                if (isset($DO["dt_date"]))
		                {
		                    if ($cond != "") $cond .= ", " ;
		                    if ($DO["dt_date"] !== "")    $cond .= "'" . addslashes($DO["dt_date"]) . "'" ;
		                    else $cond .= " NULL" ; }
		                $sql .= $cond ;

						$sql .= " ) ";
						$primera = false;
						}

                //SQL Execution
                $comm = new DBCommandClass($this->conn, $sql) ;
                $status = $comm->execute() ;
                if (!$status && $this->conn->error_level == 1062) $status = -2 ;
                //Only for rows inserted with an auto_increment as a PK
                $this->lastid = $comm->lastid ;
                return($status) ;

		}

        ////////////////////
        // UPDATE METHODS //
        ////////////////////

        //Update Methods using PK
        function updateBy_pk($DO, $where="")
        {
            $can_update = true ;
            if (is_array($this->noDuplicateFields) || is_array($this->noDuplicateUniqueKey))
            {
                $where_select = "" ;
                if (is_array($this->noDuplicateFields)) {
                    foreach ($this->noDuplicateFields as $val) {
                        if ($where_select != "") $where_select .= " OR " ;
                        $where_select .= $val . " = '" . addslashes($DO[$val]) . "'" ;
                    }
                } else {
                    foreach ($this->noDuplicateUniqueKey as $val) {
                        if ($where_select != "") $where_select .= " AND " ;
                        $where_select .= $val . " = '" . addslashes($DO[$val]) . "'" ;
                    }
                }

                if ($where_select != "") {
                    $sql = "SELECT * FROM " . $this->get_table_name('t_user_email_tmp') . " WHERE (" . $where_select . ") AND NOT (fk_i_user_id = '" . addslashes($DO["fk_i_user_id"]) . "')";

                    //Recordset Object
                    $rs = new DBRecordsetClass($this->conn, $sql) ;
                    $rs->query() ;

                    //If it returns results we cannot do the update
                    if (!$rs->movenext()) $can_update = false ;
                }
            }

            if ($can_update)
            {
                $sql  = "UPDATE " . $this->get_table_name('t_user_email_tmp') . " SET " ;

                $cond = "" ;
                if (isset($DO["fk_i_user_id"])) {
                    if ($cond != "") $cond .= ", " ;
                    if ($DO["fk_i_user_id"] != "")     $cond .= "fk_i_user_id = " . addslashes($DO["fk_i_user_id"]) ;
                    else $cond .= "fk_i_user_id = NULL" ;
                }
                
                if (isset($DO["s_new_email"])) {
                    if ($cond != "") $cond .= ", " ;
                    $cond .= "s_new_email = '" . addslashes($DO["s_new_email"]) . "'" ;
                }
                
                if (isset($DO["dt_date"])) {
                    if ($cond != "") $cond .= ", " ;
                    if ($DO["dt_date"] != "")     $cond .= "dt_date = '" . addslashes($DO["dt_date"]) . "'" ;
                    else $cond .= "dt_date = NULL" ;
                }
                $sql .= $cond . " WHERE fk_i_user_id = '" . addslashes($DO["fk_i_user_id"]) . "'" ;
                if ($where != "") $sql .= " AND " . $where ;

                //SQL Execution
                $comm = new DBCommandClass($this->conn, $sql) ;
                $status = $comm->execute() ;
                if (!$status && $this->conn->error_level != 0) {
                    if ($this->conn->error_level == 1062) $status = -2 ;
                    else $status = -1 ;
                }
                return ($status) ;
            } else {
                //It is not atomic
                return (-2) ;
            }
        }
        
        
        //Update method using a where
        function update($DO, $where)
        {
            $sql  = "UPDATE " . $this->get_table_name('t_user_email_tmp') . " SET " ;

            $cond = "" ;
            if (isset($DO["fk_i_user_id"])) {
                if ($cond != "") $cond .= ", " ;
                if ($DO["fk_i_user_id"] != "") $cond .= "fk_i_user_id = " . addslashes($DO["fk_i_user_id"]) ;
                else $cond .= "fk_i_user_id = NULL" ;
            }
                
            if (isset($DO["s_new_email"])) {
                if ($cond != "") $cond .= ", " ;
                $cond .= "s_new_email = '" . addslashes($DO["s_new_email"]) . "'" ;
            }
                
            if (isset($DO["dt_date"])) {
                if ($cond != "") $cond .= ", " ;
                if ($DO["dt_date"] != "") $cond .= "dt_date = '" . addslashes($DO["dt_date"]) . "'" ;
                else $cond .= "dt_date = NULL" ;
            }
                
            $sql .= $cond . " WHERE " . $where ;

            //SQL Execution
            $comm = new DBCommandClass($this->conn, $sql) ;
            $status = $comm->execute() ;
            if (!$status && $this->conn->error_level != 0) {
                if ($this->conn->error_level == 1062) $status = -2 ;
                else $status = -1 ;
            }
            return ($status) ;
        }


        ////////////////////
        // DELETE METHODS //
        ////////////////////

        //Deleting using a where
        function delete($where="")
        {
            $sql  = "DELETE " ;
            $sql .= "FROM " . $this->get_table_name('t_user_email_tmp') . " " ;
            if ($where != "") $sql .= " WHERE " . $where ;

            //SQL Execution
            $comm = new DBCommandClass($this->conn, $sql) ;
            return ($comm->execute()) ;
        }
        
        //Delete Method using a PK
        function deleteBy_pk($fk_i_user_id, $where="")
        {
            $sql  = "DELETE " ;
            $sql .= "FROM " . $this->get_table_name('t_user_email_tmp') . " " ;
            $sql .= "WHERE fk_i_user_id = '" . addslashes($fk_i_user_id) . "'" ;
            if ($where != "") $sql .= " AND " . $where ;

            //SQL Execution
            $comm = new DBCommandClass($this->conn, $sql) ;
            return ($comm->execute()) ;
        }
    }
?>