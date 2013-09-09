<?php
	require_once(ROOT_DIR.'/lib/core/Object.class.php');
	require_once(ROOT_DIR.'/lib/core/Iplist.class.php');
	require_once(ROOT_DIR.'/lib/core/NetworkinterfaceStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/User.class.php');
	
	class Networkinterface extends Object {
		private $networkinterface_id = 0;
		private $router_id = 0;
		private $name = "";
		private $mac_addr = "";
		private $statusdata = null;
		private $statusdata_history = null;
		private $iplist = null;
		
		public function __construct($networkinterface_id=false, $router_id=false, $name=false,
									$create_date=false, $update_date=false) {
			$this->setNetworkinterfaceId($networkinterface_id);
			$this->setRouterId($router_id);
			$this->setName($name);
			$this->setCreateDate($create_date);
			$this->setUpdateDate($update_date);
		}
		
		public function fetch() {
			$result = array();
			try {
				$stmt = DB::getInstance()->prepare("SELECT *
													FROM interfaces
													WHERE
														(id = :networkinterface_id OR :networkinterface_id=0) AND
														(router_id = :router_id OR :router_id=0) AND
														(name = :name OR :name='') AND
														(create_date = FROM_UNIXTIME(:create_date) OR :create_date=0) AND
														(update_date = FROM_UNIXTIME(:update_date) OR :update_date=0)");
				$stmt->bindParam(':networkinterface_id', $this->getNetworkinterfaceId(), PDO::PARAM_INT);
				$stmt->bindParam(':router_id', $this->getRouterId(), PDO::PARAM_INT);
				$stmt->bindParam(':name', $this->getName(), PDO::PARAM_STR);
				$stmt->bindParam(':create_date', $this->getCreateDate(), PDO::PARAM_INT);
				$stmt->bindParam(':update_date', $this->getUpdateDate(), PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo $e->getMessage();
				echo $e->getTraceAsString();
			}
			
			if(!empty($result)) {
				$this->setNetworkinterfaceId((int)$result['id']);
				$this->setRouterId((int)(int)$result['router_id']);
				$this->setName($result['name']);
				$this->setCreateDate($result['create_date']);
				$this->setUpdateDate($result['update_date']);
				$this->setStatusdata();
				$this->setIplist((int)$result['id']);
				return true;
			}
			
			return false;
		}
		
		public function store() {
			$networkinterface = new Networkinterface(false, $this->getRouterId(), $this->getName());
			$networkinterface->fetch();
//			var_dump($networkinterface);
			
			if($this->getNetworkinterfaceId() != 0 AND $networkinterface->getNetworkinterfaceId()==$this->getNetworkinterfaceId()) {
				try {
					$stmt = DB::getInstance()->prepare("UPDATE interfaces SET
																router_id = ?,
																name = ?,
																update_date = NOW()
														WHERE id=?");
					$stmt->execute(array($this->getRouterId(), $this->getName(), $this->getNetworkinterfaceId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			} elseif($this->getRouterId()!=0 AND $this->getName()!="" AND $networkinterface->getNetworkinterfaceId()==0) {
				try {
					$stmt = DB::getInstance()->prepare("INSERT INTO interfaces (router_id, name, create_date, update_date)
														VALUES (?, ?, NOW(), NOW())");
					$stmt->execute(array($this->getRouterId(), $this->getName()));
					return DB::getInstance()->lastInsertId();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			
			return false;
		}
		
		public function delete() {
			if($this->getNetworkinterfaceId() != 0) {
				//first delete all assigned ip addresses
				$iplist = new Iplist($this->getNetworkinterfaceId());
				$iplist->delete();
				
				//then delete interface
				try {
 					$stmt = DB::getInstance()->prepare("DELETE FROM interfaces WHERE id=?");
					$stmt->execute(array($this->getNetworkinterfaceId()));
					return $stmt->rowCount();
				} catch(PDOException $e) {
					echo $e->getMessage();
					echo $e->getTraceAsString();
				}
			}
			return false;
		}
		
		public function setNetworkinterfaceId($networkinterface_id) {
			if(is_int($networkinterface_id))
				$this->networkinterface_id = $networkinterface_id;
		}
		
		public function setRouterId($router_id) {
			if(is_int($router_id))
				$this->router_id = $router_id;
		}
		
		public function setName($name) {
			if(is_string($name))
				$this->name = $name;
		}
		
		public function setMacAddr($mac_addr) {
			if(is_string($mac_addr))
				$this->mac_addr = $mac_addr;
		}
		
		public function setStatusdata($statusdata=false) {
			if($statusdata!=false) {
				$this->statusdata = $statusdata;
			} else {
				$networkinterface_status = new NetworkinterfaceStatus(false, false, (int)$this->getNetworkinterfaceId());
				$networkinterface_status->fetch();
				$this->statusdata = $networkinterface_status;
			}
		}
		
		public function setIplist($iplist=false) {
			if($iplist!=false && is_array($iplist)) {
				$this->iplist = $iplist;
			} else {
				$this->iplist = new Iplist($this->networkinterface_id);
			}
		}
		
		public function getNetworkinterfaceId() {
			return $this->networkinterface_id;
		}
		
		public function getRouterId() {
			return $this->router_id;
		}
		
		public function getName() {
			return $this->name;
		}
		
		public function getMacAddr() {
			return $this->mac_addr;
		}
		
		public function getStatusdata() {
			return $this->statusdata;
		}
		
		public function getIpList() {
			return $this->iplist;
		}
		
		public function getRouter() {
			$router = new Router($this->getRouterId());
			if($router->fetch())
				return $router;
			return false;
		}
		
		public function getDomXMLElement($domdocument) {
			$domxmlelement = $domdocument->createElement('networkinterface');
			$domxmlelement->appendChild($domdocument->createElement("networkinterface_id", $this->getNetworkinterfaceId()));
			$domxmlelement->appendChild($domdocument->createElement("router_id", $this->getRouterId()));
			$domxmlelement->appendChild($domdocument->createElement("name", $this->getName()));
			$domxmlelement->appendChild($domdocument->createElement("mac_addr", $this->getMacAddr()));
			$domxmlelement->appendChild($domdocument->createElement("create_date", $this->getCreateDate()));
			
			$domxmlelement->appendChild($this->getStatusdata()->getDomXMLElement($domdocument));
			
			$domxmliplist = $domdocument->createElement('iplist');
			$domxmliplist->appendChild($this->getIplist()->getDomXMLElement($domdocument));
			$domxmlelement->appendChild($domxmliplist);
			return $domxmlelement;
		}
	}
?>