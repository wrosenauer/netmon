<?php
	class Object {
		protected $create_date;
		protected $update_date;
		
		public function setCreateDate($create_date=false) {
			if($create_date == false)
				$this->create_date = time();
			else if(is_string($create_date)) {
				$date = new DateTime($create_date);
				$this->create_date = $date->getTimestamp();
			} else if(is_int($create_date))
				$this->create_date = $create_date;
		}
		
		public function setUpdateDate($update_date) {
			if($update_date == false)
				$this->update_date = time();
			else if(is_string($update_date)) {
				$date = new DateTime($update_date);
				$this->update_date = $date->getTimestamp();
			} else if(is_int($update_date))
				$this->update_date = $update_date;
		}
		
		public function getCreateDate() {
			return $this->create_date;
		}
		
		public function getUpdateDate() {
			return $this->update_date;
		}
	}
?>