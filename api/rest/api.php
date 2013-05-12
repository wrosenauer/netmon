<?php
	require_once("../../config/config.local.inc.php");
	require_once("../../lib/classes/core/db.class.php");
	require_once("../../lib/classes/core/Iplist.class.php");
	require_once("../../lib/classes/core/Ip.class.php");
	require_once("../../lib/classes/core/Networkinterface.class.php");
	require_once("../../lib/classes/core/Event.class.php");
	require_once("../../lib/classes/extern/rest/rest.inc.php");
	
	class API extends Rest {
		private $domxml = null;
		private $domxmlresponse = null;
		
		private $authentication = true;
		private $api_key = "";
		private $method = "";
		private $error_code = 0;
		private $error_message = "";
	
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->domxml = new DOMDocument('1.0', 'utf-8');
			$this->domxmlresponse = $this->domxml->createElement("netmon_response");
		}
		
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if(isset($_POST['api_key']))
				$this->api_key = $_POST['api_key'];
			elseif(isset($_GET['api_key']))
				$this->api_key = $_GET['api_key'];
			$this->method = $func;
			
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		
		private function iplist() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$iplist = new Iplist($this->_request['id']);
				$domxmldata = $iplist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} elseif($this->get_request_method() == "GET") {
				$iplist = new Iplist();
				$domxmldata = $iplist->getDomXMLElement($this->domxml);
				$this->response($this->finishxml($domxmldata), 200);
			} else {
				$this->error_code = 2;
				$this->error_message = "The iplist could not be created, your request seems to be malformed.";
				$this->response($this->finishxml(), 400);
			}
		}
		
		private function ip() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$ip = new Ip((int)$this->_request['id']);
				if($ip->getIpId() == 0) {
					$this->error_code = 1;
					$this->error_message = "IP not found";
					$this->response($this->finishxml(), 404);
				} else {
					$domxmldata = $ip->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				}
			} elseif ($this->get_request_method() == "GET" && count($_GET) == 1) {
				header('Location: http://netmon.freifunk-ol.de/api/rest/iplist/');
			}
		}
		
		private function networkinterface() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$networkinterface = new Networkinterface((int)$this->_request['id']);
				if($networkinterface->getInterfaceId() == 0) {
					$this->error_code = 1;
					$this->error_message = "Networkinterface not found";
					$this->response($this->finishxml(), 404);
				} else {
					$domxmldata = $networkinterface->getDomXMLElement($this->domxml);
					$this->response($this->finishxml($domxmldata), 200);
				}
			} elseif ($this->get_request_method() == "GET" && count($_GET) == 1) {
				header('Location: http://netmon.freifunk-ol.de/api/rest/networkinterfaclist/');
			}
		}
		
		private function event() {
			if($this->get_request_method() == "GET" && isset($this->_request['id'])) {
				$event = new Event((int)$this->_request['id']);
				if($event->getEventId() == 0) {
					$this->error_code = 1;
					$this->error_message = "Event not found";
					$this->response($this->finishxml(), 404);
				} else {
					$domxmldata = $this->domxml->createElement("event");
					$domxmldata->appendChild($this->domxml->createElement("event_id", $event->getEventId()));
//					$domxmldata->appendChild($this->domxml->createElement("crawl_cycle_id", $event->getCrawlCycleId()));
					$domxmldata->appendChild($this->domxml->createElement("object", $event->getObject()));
					$domxmldata->appendChild($this->domxml->createElement("object_id", $event->getObjectId()));
					$domxmldata->appendChild($this->domxml->createElement("action", $event->getAction()));
					$domxmldata->appendChild($this->domxml->createElement("create_date", $event->getCreateDate()));
					$data = $this->domxml->createElement("data");
					$this->fromMixed($event->getData(), $data);
					$domxmldata->appendChild($data);
  					
					$this->response($this->finishxml($domxmldata), 200);
				}
			} elseif ($this->get_request_method() == "GET" && count($_GET) == 1) {
				header('Location: http://netmon.freifunk-ol.de/api/rest/events/');
			} elseif ($this->get_request_method() == "POST" OR 
			         ($this->get_request_method() == "GET" && !isset($this->_request['id']) && count($_GET)>1)) {
			    if($this->isApiKeyValid($this->api_key)) {
					$data = (!empty($_POST)) ? $_POST : $_GET;
					
					$event = new Event(false, $data['object'], $data['object_id'], $data['action'], $data['data']);
					$event_id = $event->store();
					if($event_id!=false) {
						header('Location: http://netmon.freifunk-ol.de/api/rest/event/'.$event_id);
					} else {
						$this->authentication=false;
						$this->error_code = 2;
						$this->error_message = "The Event could not be created. Your request seems to miss some data.";
						$this->response($this->finishxml(), 400);
					}
				} else {
						$this->error_code = 2;
						$this->error_message = "The api_key is not valid.";
						$this->response($this->finishxml(), 401);
				}
			} else {
				$this->response('',406);
			}
		}
		
		private function events() {
			$this->response($this->finishxml(), 200);
		}
		
		public function fromMixed($mixed, DOMElement $domElement = null) {
			if (is_array($mixed)) {
				foreach( $mixed as $index => $mixedElement ) {
					if ( is_int($index) ) {
						if ( $index == 0 ) {
							$node = $domElement;
						} else {
							$node = $this->domxml->createElement($domElement->tagName);
							$domElement->parentNode->appendChild($node);
						}
					} else {
						$node = $this->domxml->createElement($index);
						$domElement->appendChild($node);
					}
					
					$this->fromMixed($mixedElement, $node);
				}
			} else {
				$domElement->appendChild($this->domxml->createTextNode($mixed));
			}
		}
		
		public function isApiKeyValid($api_key) {
			if(!empty($api_key)) {
				$stmt = DB::getInstance()->prepare("SELECT * FROM users WHERE api_key=?");
				$stmt->execute(array($api_key));
				return $stmt->rowCount();
			} else {
				return false;
			}
		}
		
		/*
		 *	Encode array into JSON
		*/
		private function finishxml($domxmldata=false){
				$domxmlrequest = $this->domxml->createElement("request");
				$domxmlrequest->appendChild($this->domxml->createElement("authentication", $this->authentication));
				$domxmlrequest->appendChild($this->domxml->createElement("api_key", $this->api_key));
				$domxmlrequest->appendChild($this->domxml->createElement("method", $this->method));
				$domxmlrequest->appendChild($this->domxml->createElement("error_code", $this->error_code));
				$domxmlrequest->appendChild($this->domxml->createElement("error_message", $this->error_message));
				$this->domxmlresponse->appendChild($domxmlrequest);
				if($domxmldata!=false)
					$this->domxmlresponse->appendChild($domxmldata);
				$this->domxml->appendChild($this->domxmlresponse);
			
				return $this->domxml->saveXML();
		}
	}
	// Initiiate Library
	
	$api = new API;
	$api->processApi();
?>