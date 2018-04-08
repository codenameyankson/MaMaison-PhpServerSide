
<?php

/**
 * Class DatabaseConnection
 * This class creates a connection to the database. The signleton design pattern is used for the database connection.
 */

/**
 * connect to the database
 * This fine defines the database credentials and establishes a connection to
 * the database. It also ensures that, the connection which is being returned is
 * a valid connection. otherwise, the page dies off.
 **/
class DatabaseConnection
{

    /* instance variables of the class. They are private to limit their visibility */
    private $databaseConnection = null;
    private $results;

    /**
     * return an instance of this the database connection
     */
    private function getInstance()
    {
        //create new connection if its doesn't exist already
        if ($this->databaseConnection == null) {
            $this->databaseConnection = new mysqli("localhost", "root", "king", "MaMaison");
        }
        //exit if database connection fails
        if ($this->databaseConnection->connect_error) {
            die("The connection to the database failed");
        }
        return true; //return connection for use
    }

    /**
     * @param $query a string to query the database
     * @return bool true if the connection was successful. Return false otherwise
     */

    public function query($query)
    {
        if ($this->databaseConnection == null) {
            $this->getInstance(); //if connection has not been created, create one
            //perform connection
        }

        $this->results = $this->databaseConnection->query($query);

        return $this->results == true;
    }

    /**
     * @return False if the results has not been set. Return the results otherwise
     */
    public function getResults()
    {
        if ($this->results == false) {
            return false;
        }
        $vals = array();
        while ($row = $this->results->fetch_assoc()) {
            $vals[] = $row; // this is returing a 2-d array

        }
        return $vals;
    }


    /**
     * a function to execute a prepared sql statemtn
     * @param $sql string  the given sql statement to prepare
     * @param $paramTypes string  the type of the parameters
     * @param $params array the arguments to bind
     * @return bool return true if the execution was successful. return false otherwise.
     */
    function prepareSql($sql, $paramTypes, $params)
    {
        if ($this->databaseConnection == null) {
            $this->getInstance();
        }
// prepare sql statement and bind parameters

        $statement = $this->databaseConnection->prepare($sql);
        $refs = array();
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key]; //get the reference of the database
        }
        $val = array_merge(array($paramTypes), $refs);
        call_user_func_array(array($statement, "bind_param"), $val);

        if ($statement == false) {
            return false;
        }

        $correct = $statement->execute();
        if ($correct == false) {
            return false;
        } else {
            $this->results = $statement->get_result();
            return true;
        }

        $statement = $this->databaseConnection->prepare($sql);
        $refs = array();
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        $val = array_merge(array($paramTypes), $refs);
        call_user_func_array(array($statement, "bind_param"), $val);

        if ($statement == false) {
            return false;
        }

        $correct = $statement->execute();
        if ($correct == false) {
            return false;
        } else {
            $this->results = $statement->get_result();
            return true;
        }
    }

        /**
         * A function to perform the generic select * from the database query
         * @param $query string  the generic query to perform
         * @return string of the results
         */
        public  function getAllTableContents($query)
        {
            $results = array();
            //perform query and return results
            if ($this->query($query)) {
                $results = $this->getResults();
            }
            return $results;

        }


    /**
     * @return string an error if there is any.
     */
    public function getError()
    {
        if ($this->databaseConnection->error != null) {
            return $this->databaseConnection->error;
        } else {
            return "No errors recorded";
        }
    }

    /**
     * A function to return the number of rows in the mysql results
     * @return bool false if the results has not beeen set. Returns the number of rows otherwise
     */
    public function getRows()
    {
        if ($this->results == false) {
            return false;
        } else {
            return $this->results->num_rows;
        }
    }

    /**
     * A function to reflect the number of rows affected by a query. for updates and deletee
     * @return bool false if no rows were affected. returns the number of rows otherwise
     */
    public  function getNumRowsAffected(){
        if($this -> databaseConnection != null){
            return $this -> databaseConnection -> affected_rows;
        }else {
            return false;
        }
    }

}

?>
