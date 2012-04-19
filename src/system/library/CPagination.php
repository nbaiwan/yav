<?php

class CPagination extends Component {
    const DEFAULT_PAGE_SIZE = 10;
    public $pageVar = 'page';
    
    private $_itemCount = 0;
    private $_pageSize = self::DEFAULT_PAGE_SIZE;
    private $_currentPage = null;
    
    public function __construct($count, $pagesize = self::DEFAULT_PAGE_SIZE) {
        $this->_itemCount = $count;
        $this->_pageSize = $pagesize;
    }
    
    public function getPageSize() {
        return $this->_pageSize;
    }
    
    public function setPageSize($pagesize) {
        $this->_pageSize = $pagesize > 0 ? $pagesize : self::DEFAULT_PAGE_SIZE;
    }
    
    public function getPageCount() {
        return ceil($this->_itemCount / $this->_pageSize);
    }
    
	public function getItemCount() {
		return $this->_item_count;
	}
    
	public function setItemCount($count) {
		$this->_item_count = $count;
	}
    
    public function getCurrentPage($recalculate = true) {
		if($this->_currentPage===null || $recalculate) {
            
            if(isset($_GET[$this->pageVar])) {
                $this->_currentPage = intval($_GET[$this->pageVar]) > 0 ? intval($_GET[$this->pageVar]) : 1;
                if($this->_currentPage > $this->getPageCount()) {
                    $this->_currentPage = $this->getPageCount();
                }
            } else {
                $this->_currentPage = 1;
            }
        }
        
        return $this->_currentPage;
    }
    
    public function setCurrentPage($page) {
        $this->_currentPage = $page;
        $_GET[$this->pageVar] = $page;
    }
    
    public function applyLimit(&$params) {
		$params['limit'] = $this->getLimit();
		$params['offset'] = $this->getOffset();
    }
    
	public function getOffset() {
		return ($this->getCurrentPage() - 1) * $this->_pageSize;
	}
    
	public function getLimit() {
		return $this->_pageSize;
	}
}