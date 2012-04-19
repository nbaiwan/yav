<?php
/**
 *
 */

class DefaultController extends Controller {

    public function indexAction($page = 1) { /* 默认Action */
        $mongo = new Mongo('mongodb://127.0.0.1:30000');
        $db = new MongoDB($mongo, 'test');
        
        $rs = $db->user->find();
        echo $rs->count();
        
        $offset = ($page - 1) * 10;
        $rs->skip($offset)->limit(10);
        while($rs->hasNext()) {
            $row = $rs->getNext();
            echo $row['id'], '<br />';
        }
    }
   
    public function db1Action() { /* 默认Action */
        
		if(isset($_GET['debug'])) {
			xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
		}
        
        $s = microtime(true);
        $config = $this->config->component->db->toArray();
        $db = new PDO($config['params']['dsn'], $config['params']['username'], $config['params']['password']);
        $sql = "SELECT region_id FROM qvod_district_region LIMIT 0, 10";
        
        for($i=0; $i< 1000; $i++) {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $stmt->fetchAll(PDO::FETCH_ASSOC);
            //print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        $e = microtime(true);
        echo $e - $s;
        
        
        //$rs = $db->query($sql);
        //print_r($rs->fetchAll(PDO::FETCH_ASSOC));
        
		if(isset($_GET['debug'])) {
			$xhprofData = xhprof_disable();
			
			define('XHPROF_ROOT', APP_PATH . '/webroot/xhprof');
			
			include_once(XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php');
			include_once(XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php');
			$xhprofRuns = new XHProfRuns_Default();
			$runId = $xhprofRuns->save_run($xhprofData, 'testSerialize');
			echo "http://iploc.kuaibo.com/xhprof/xhprof_html/index.php?run={$runId}&source=testSerialize";
		}
		exit;
        exit;
    }
   
    public function db2Action() { /* 默认Action */
        
		if(isset($_GET['debug'])) {
			xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
		}
        
        $s = microtime(true);
        for($i=0; $i< 1000; $i++) {
            $sql = "SELECT region_id FROM qvod_district_region LIMIT 0, 10";
            $stmt = $this->db->createCommand($sql);
            //print_r($stmt->queryAll());
            $stmt->queryAll();
        }
        
        $e = microtime(true);
        echo $e - $s;
        
		if(isset($_GET['debug'])) {
			$xhprofData = xhprof_disable();
			
			define('XHPROF_ROOT', APP_PATH . '/webroot/xhprof');
			
			include_once(XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php');
			include_once(XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php');
			$xhprofRuns = new XHProfRuns_Default();
			$runId = $xhprofRuns->save_run($xhprofData, 'testSerialize');
			echo "http://iploc.kuaibo.com/xhprof/xhprof_html/index.php?run={$runId}&source=testSerialize";
		}
		exit;
        exit;
    }
}

?>
