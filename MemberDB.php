<?php
require_once("DBManager.php");
class MemberDB extends DBManager{
	// add one member into member database
	private function addAMember($account,$password,$userName,$email){
		$query=$this->db->prepare("insert into member (Account,Password,Username,Email) values(?,?,?,?)");
		$query->execute( array($account,$password,$userName,$email) );
	}
	// this method is to get the user's password
	public function getPassword($account,$userName){
		$result=array( "status"=>"","result"=>array() );
		
		try{
			$query=$this->db->prepare("select Password from member where Account=:account and Username=:userName");
			$query->bindValue(":account",$account);
			$query->bindValue(":userName",$userName);
			
			if( $query->execute() ){
				$data=$query->fetchAll( PDO::FETCH_ASSOC );
				$number=count($data);
				
				if( $number==0 ){
					$result["status"]="warning";
					$result["status"]["message"]="no data";
				}
				else{
					$result["status"]="Success";
					$result["result"]["data"]=$data;
				}
			}
			else{
				$result["status"]="error";
				$result["status"]["message"]="SQL error";
			}
		}
		catch( PDOException $exception ){
			$result["status"]="error";
			$result["status"]["message"]="Exception ".$exception->getMessage();
		}
		
		return json_encode( $result );
	}
	public function getUserInformation($account,$password){
		$result=array( "status"=>"","result"=>array() );
		
		try{
			$query=$this->db->prepare("select * from member where Account=:account and Password=:password");
			$query->bindValue(":account",$account);
			$query->bindValue(":password",$password);
			
			if( $query->execute() ){
				$data=$query->fetchAll( PDO::FETCH_ASSOC );
				
				if( count( $data )==0 ){
					$result["status"]="warning";
					$result["status"]["message"]="no data";
				}
				else{
					$result["status"]="Success";
					$result["result"]["data"]=$data;
				}
			}
			else{
				$result["status"]="error";
				$result["status"]["message"]="SQL error";
			}
		}
		catch( PDOException $exception ){
			$result["status"]="error";
			$result["status"]["message"]="Exception ".$exception->getMessage();
		}
		
		return json_encode( $result );
	}
	public function signUp($account,$password,$userName,$email){
		$result=array( "status"=>"","result"=>array() );
		
		try{
			$query=$this->db->prepare("select Account from member where Account=:account");
			$query->bindValue(":account",$account);
			
			if( $query->execute() ){
				$data=$query->fetchAll( PDO::FETCH_ASSOC );
				$number=count($data);
				
				if( $number==0 ){// the account has not been used
					$this->addAMember($account,$password,$userName,$email);
					$result["status"]="Success";
					$result["data"]="sign up done";
				}
				else{ // the account has been used
					$result["status"]="warning";
					$result["data"]="The account has been used";
				}
			}
			else{
				$result["status"]="error";
				$result["status"]["message"]="SQL error";
			}
		}
		catch( PDOException $exception ){
			$result["status"]="error";
			$result["status"]["message"]="Exception ".$exception->getMessage();
		}
		
		return json_encode( $result );
	}
	public function existThisUser($account,$password){
		$result=array( "status"=>"","result"=>array() );
		
		try{
			$query=$this->db->prepare("select * from member where Account=:account and Password=:password");
			$query->bindValue(":account",$account);
			$query->bindValue(":password",$password);
			
			if( $query->execute() ){
				$data=$query->fetchAll( PDO::FETCH_ASSOC );
				if( count($data)>0 ){
					$result["status"]="Success";
					$result["data"]="true";
				}
				else{
					$result["status"]="Success";
					$result["data"]="false";
				}
			}
			else{
				$result["status"]="error";
				$result["status"]["message"]="SQL error";
			}
		}
		catch( PDOException $exception ){
			$result["status"]="error";
			$result["status"]["message"]="Exception ".$exception->getMessage();
		}
		
		return json_encode( $result );
	}
}	
?>