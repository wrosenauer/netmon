<?php
	//This script can only be called by the server
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		die("This script can only be run by the server directly.");
	}
	
	/**
	 * Necessary includes
	 **/
	require_once('runtime.php');
	require_once(ROOT_DIR.'/lib/core/crawling.class.php');
	require_once(ROOT_DIR.'/lib/core/router_old.class.php');
	require_once(ROOT_DIR.'/lib/core/rrdtool.class.php');
	require_once(ROOT_DIR.'/lib/core/Eventlist.class.php');
	require_once(ROOT_DIR.'/lib/core/RouterStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/Networkinterfacelist.class.php');
	require_once(ROOT_DIR.'/lib/core/NetworkinterfaceStatus.class.php');
	require_once(ROOT_DIR.'/lib/core/EventNotificationList.class.php');
	
	/**
	 * Crawlcycle organisation
	 **/
	echo "Organizing crawl cycles\n";
	//Get crawl cycle data
	$actual_crawl_cycle = Crawling::getActualCrawlCycle();
	$crawl_cycle_start_buffer = ConfigLine::configByName("crawl_cycle_start_buffer");
	if(empty($actual_crawl_cycle) OR strtotime($actual_crawl_cycle['crawl_date'])+(($GLOBALS['crawl_cycle']-$crawl_cycle_start_buffer)*60)<=time()) {
		echo "Create crawl data for offline routers\n";
		//Set all routers in old crawl cycle that have not been crawled yet to status offline
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
												FROM routers
												WHERE id not in (SELECT router_id
																 FROM crawl_routers
																 WHERE crawl_cycle_id=$actual_crawl_cycle[id])");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		foreach($result as $router) {
			echo "	Inserting offline data for router ".$router['hostname']."\n";
			//store offline crawl for offline router
			$router_status = New RouterStatus(false, (int)$actual_crawl_cycle['id'], (int)$router['id'], "offline");
			$router_status->store();
		}
		
		echo "Create crawl data for offline interfaces\n";
		//store offline crawl for all interfaces of offline router
		try {
			$stmt = DB::getInstance()->prepare("SELECT *
												FROM interfaces
												WHERE id not in (SELECT interface_id
																 FROM crawl_interfaces
																 WHERE crawl_cycle_id=$actual_crawl_cycle[id])");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
		}
		foreach($result as $interface) {
			echo "	Inserting offline data for interface ".$interface['name']."\n";
			$networkinterface_status = new NetworkinterfaceStatus(false, (int)$actual_crawl_cycle['id'],
																  (int)$interface['id'], (int)$interface['router_id']);
			$networkinterface_status->store();
		}
		
		echo "Close old crawl cycle and create new one\n";
		//Create new crawl cycle and close old crawl cycle
		//Create new crawl cycle
		Crawling::newCrawlCycle();
		//Close old Crawl cycle
		Crawling::closeCrawlCycle($actual_crawl_cycle['id']);
		
		echo "Create graph statistics\n";
		//Make statistic graphs
		$online = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'online');
		$offline = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'offline');
		$unknown = Router_old::countRoutersByCrawlCycleIdAndStatus($actual_crawl_cycle['id'], 'unknown');
		$total = $unknown+$offline+$online;
		RrdTool::updateNetmonHistoryRouterStatus($online, $offline, $unknown, $total);
		
		$client_count = Router_old::countRoutersByCrawlCycleId($actual_crawl_cycle['id']);
		RrdTool::updateNetmonClientCount($client_count);
	} else {
		echo "There is an crawl cycle running actually. Doing nothing.\n";
	}
	
	/**
	 * Clean database
	 */
	echo "Clean database\n";
	
	//Delete old Crawls
	echo "Remove old crawl data\n";
	Crawling::deleteOldCrawlDataExceptLastOnlineCrawl(($GLOBALS['hours_to_keep_mysql_crawl_data']*60*60));
	
	//Remove old events
	echo "Remove old events\n";
	$secondsToKeepHistoryTable = 60*60*$GLOBALS['hours_to_keep_history_table'];
	try {
		$stmt = DB::getInstance()->prepare("DELETE FROM events WHERE UNIX_TIMESTAMP(create_date) < UNIX_TIMESTAMP(NOW())-?");
		$stmt->execute(array($secondsToKeepHistoryTable));
	} catch(PDOException $e) {
		echo $e->getMessage();
		echo $e->getTraceAsString();
	}
	
	//Remove old not assigned routers
	echo "Remove not assigned routers that haven´t been updated for a while\n";
	DB::getInstance()->exec("DELETE FROM routers_not_assigned WHERE TO_DAYS(update_date) < TO_DAYS(NOW())-2");
	
	/**
	* Crawl
	**/
	echo "Do crawling\n";
	
	//Crawl routers
	echo "Crawl routers\n";

	if (!isset($GLOBALS['crawllog']))
		$GLOBALS['crawllog'] = false;

	if ($GLOBALS['crawllog'] && !is_dir(ROOT_DIR."/logs/")) {
		mkdir(ROOT_DIR."/logs/");         
	}
	
	$range = ConfigLine::configByName("crawl_range");
	$routers_count = Router_old::countRouters();
	for ($i=0; $i<=$routers_count; $i+=$range) {
		//start an independet crawl process for each $range routers to crawl routers simultaniously
		$return = array();
		if ($GLOBALS['crawllog'])
			$logcmd = "> ".ROOT_DIR."/logs/crawler_".$i."-".($i+$range).".txt";
		else
			$logcmd = "> /dev/null";
		$cmd = "php ".ROOT_DIR."/integrated_xml_ipv6_crawler.php -o".$i." -l".$range." ".$logcmd." & echo $!";
		echo "Initializing crawl process to crawl routers ".$i." to ".($i+$range)."\n";
		echo "Running command: $cmd\n";
		exec($cmd, $return);
		echo "The initialized crawl process has the pid $return[0]\n";
	}
	
	/**
	 * Notifications
	 */
	
	echo "Sending notifications\n";
	$event_notification_list = new EventNotificationList();
	$event_notification_list->notify();
	
	echo "Done\n";
?>
