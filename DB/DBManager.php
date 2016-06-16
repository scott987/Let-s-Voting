<?php
abstract class DBManager
{
    protected $db=null;

    public function __construct($host,$dbname,$usr,$password)
    {
        try {
            if ($host==null or $dbname==null or $usr==null or $password ==null) {
                include("db.ini.php");
            }
            
            $this->db=new PDO("mysql:host=".$host.";dbname=".$dbname.";charset=utf8", $usr, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_TIMEOUT, 65535);
        }
        catch(PDOException $exception) {
            echo "Database Connection Error: ".$exception->getMessage();
            throw new Exception("DB Error.");
        }
    }

    protected function resultMessage($status,$Message=null)
    {
        $result = array("status"=>"","result"=>array());
        $result["status"]=$status;
        if (!is_null($Message)) {
            $result["result"]["Message"] = $Message;
        }
        return $result;
    }

    protected function exceptionMessage($exception)
    {
        return resultMessage("error","Exception:".$exception->getMessage());
    }
}