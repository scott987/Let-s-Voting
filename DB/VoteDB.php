<?php

require_once("DBManager.php");

class VotingDB extends DBManager
{
    /**
    Create topic
    $owner is who create topic
    $name is topic name
    $option(array) is the options topic has
    $describe is the describe of topic
    $private means whether the topic is private or not
    $vertify is the vertify num
    $deadline is the deadline of topic

    return "["status"=>"","result"=>[]]"
    status=="Success":
    result = "[
        "id" => topicId
    ]"
    **/
    public function createTopic($owner,$name="new Topic", $option=["option0"], $describe="", $private=false, $vertify="", $deadline=null)
    {
        $result = array("status"=>"","result"=>array());
        try {
            $query = $this->db->prepare("insert into topic (TopicName,`Desc`,Enable,Owner,private,vertify,deadline) values (:topicName,:describe,false,:owner,:private,:vertify,:deadline);");
            $query->bindValue(':topicName', $name);
            $query->bindValue(':describe', $describe);
            $query->bindValue(':owner', (int)$owner, PDO::PARAM_INT);
            $query->bindValue(':private', $private, PDO::PARAM_BOOL);
            $query->bindValue(':vertify', $vertify);
            if ($deadline == null) {
                $deadline = strtotime(date('Y-m-d H:i:s'));
                $deadline = date('Y-m-d H:i:s',strtotime('+7 day', $deadline));
            }
            $query->bindValue(':deadline', $deadline);

            if ($query->execute()) {
                $result["status"]="Success";
                $id=$this->db->query("select last_insert_id() id")->fetch(PDO::FETCH_ASSOC)['id'];
                foreach ($option as $op) {
                    $query = $this->db->prepare("insert into `option` (OptionName,TopicId,OptionCount) values (:optionName,:topicId,0)");
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
            $result["result"]["Message"]="Exception:".$exception->getMessage();
        }
        return json_encode($result);
    }
    /**
    get a voteing information
    $id is voting topic id
    $attr is to get some attribution, if null will get all

    return "["status"=>"","result"=>[]]"
    status == "warning": no data;
    status=="Success":
    result = "[
        "data_num" => num of data,
        "data" => array of datas in the database (check the column of topic & option)
    ]"
    **/
    public function getVotingInfo($id,$attr=null)
    {
        $result = array("status"=>"","result"=>array());
        try {
            $select = "";
            if (is_null($attr)) {
                $select = "*";
            } else {
                foreach ($attr as $val) {
                    $select .= $val.",";
                }
                $select = substr($select, 0, -1);
            }
            $query = $this->db->prepare("select ".$select." from topic where TopicID = :id");
            
            $query->bindValue(':id', (int)$id, PDO::PARAM_INT);

            if ($query->execute()) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
                $data[0]["option"] = $this->db->query("select * from `option` where TopicID = ".$id)->fetchAll(PDO::FETCH_ASSOC);

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
        }
        return json_encode($result);
    }

    /**
    vote a option
    $option is the option of topic thich is voted by usr
    $usr is who vote

    return "["status"=>"","result"=>[]]"
    status=="Success":
    result = "[
        "change_num" => num of change data
    ]"
    **/
    public function vote($option,$usr="anoymous")
    {
        $result = array("status"=>"","result"=>array());
        try {
            if ($usr!="anoymous" && $this->isVoted($usr)) {
                $result["status"]="error";
                $result["result"]["Message"]="only vote once";
            } else {
                if (is_array($option)) {
                    try {
                        $this->db->beginTransaction();
                        foreach ($option as $op) {
                            $this->db->exec("update `option` set OptionCount = OptionCount + 1 where OptionId = ".$op);
                            if ($usr != "anoymous") {
                                $topic = $this->db->query("select TopicId from `option` where OptionId =".$op)->fetch(PDO::FETCH_ASSOC)["TopicId"];
                                $this->db->exec("insert into vote (UID,OptionId,TopicId) values (".$usr.",".$op.",".$topic.")");
                            }
                        }
                        $this->db->commit();
                        $result["status"]="Success";
                        $result["result"]["change_num"]=count($option);
                    }
                    catch(PDOException $exception) {
                        $this->db->rollBack();
                        $result["status"]="error";
                        $result["result"]["Message"]="Exception".$exception->getMessage();
                    }
                } else {
                    $this->db->exec("update `option` set OptionCount = OptionCount + 1 where OptionId = ".$option);
                    if($usr!="anoymous"){
                        $topic = $this->db->query("select TopicId from `option` where OptionId =".$option)->fetch(PDO::FETCH_ASSOC)["TopicId"];
                        $this->db->exec("insert into vote (UID,OptionId,TopicId) values (".$usr.",".$option.",".$topic.")");
                    }   
                    $result["status"]="Success";
                    $result["result"]["change_num"]=1;
                }
            }
        }
        catch(PDOException $exception) {
            $result["status"]="error";
            $result["result"]["Message"]="Exception".$exception->getMessage();
        }
        return json_encode($result);
    }

    protected function isVoted($usr)
    {
        return $this->db->query("select * from vote where UID = ".$usr)->fetch()?true:false;
    }
}