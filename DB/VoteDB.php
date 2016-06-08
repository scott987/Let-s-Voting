<?php
require_once("DBManager.php");

class VoteingDB extends DBManger
{
    public function createTopic($owner,$name="new Topic", $option=["選項"], $describe="")
    {
        $result = array("status"=>"","result"=>array());
        try {
            $query = $this->db->prepare("insert into vote (TopicName,Desc,Enable,Owner) values (:topicName,:describe,false,:owner)");
            $query->bindValue(':topicName', $name);
            $query->bindValue(':describe', $describe);
            $query->bindValue(':owner', (int)$owner, PDO::PARAM_INT);

            if ($query->execute()) {
                $result["status"]="Success";
                $id=$this->db->exec("select last_insert_id() id");
                foreach ($option as $op) {
                    $query = $this->db->prepare("insert into option (OptionName,TopicId,OptionCount) values (:optionName,:topicId,0)");
                    $query->bindValue(':optionName', $op);
                    $query->bindValue(':topicId', (int)$id, PDO::PARAM_INT);
                    $query->execute();
                }
                $result["result"]["id"]=$id;
            } else {
                $result["status"]="error";
                $result["result"]["Message"]="SQL error";
            }
        }
        catch(PDOException $exception) {
            $result["status"]="error";
            $result["result"]["Message"]="Exception".$exception->getMessage();
            throw new Exception("DB Error.");
        }
        return json_encode($result);
    }
    /**
    get a voteing information
    $id is voting topic id
    $attr is to get some attribution, if null will get all
    **/
    public function getVoteingInfo($id,$attr=null)
    {
        $result = array("status"=>"","result"=>array());
        try {
            $query = $this->db->prepare("select :attr from vote where TopicId = :id");
            $select = "";
            if (is_null($attr)) {
                $select = "*";
            } else {
                foreach ($attr as $val) {
                    $select .= $val.",";
                }
                $select = substr($select, 0, -1);
            }
            $query->bindValue(':attr', $select);
            $query->bindValue(':id', (int)$id, PDO::PARAM_INT);

            if ($query->execute()) {
                $data = $query->fetchAll();
               
                $result["result"]["data_num"] =count($data);
                $result["result"]["data"]=$data;

                if (count($data)==0) {
                    $result["status"]="warning";
                    $result["result"]["Message"]="no data";
                } else {
                    $result["status"]="Success";
                }
            } else {
                $result["status"]="error";
                $result["result"]["Message"]="SQL error";
            }
        }
        catch(PDOException $exception) {
            $result["status"]="error";
            $result["result"]["Message"]="Exception".$exception->getMessage();
            throw new Exception("DB Error.");
        }
        return json_encode($result);
    }

    /**
    vote a option
    $id is topic id
    $option is the option of topic thich is voted by usr
    $usr is who vote
    **/
    public function vote($id,$option,$usr="anoymous")
    {
        $result = array("status"=>"","result"=>array());
        try {
            if (isVoted($usr)) {
                $result["status"]="error";
                $result["result"]["Message"]="僅能投票一次";
            } else {
                //TODO retrun meesage
            }
        }
        catch(PDOException $exception) {
            $result["status"]="error";
            $result["result"]["Message"]="Exception".$exception->getMessage();
            throw new Exception("DB Error.");
        }
        return json_encode($result);
    }

    /**
    TODO
    **/
    protected function isVoted($usr)
    {
        return true;
    }
}