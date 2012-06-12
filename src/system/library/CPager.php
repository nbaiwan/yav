<?php
/**
 * CPager分页类
 * @version $Id: library/CPager.php Apr 17, 2012 11:37:02 AM
 * @author Jacky Zhang <myself.fervor@gmail.com>
 * @copyright 启航网络科技
 */

class CPager extends Component {
	/**
	 * 总页数
	 * @var int
	 */
	const DEFAULT_TOTAL_PAGE = 1;
	/**
	 * 当前页
	 * @var int
	 */
	const DEFAULT_NOW_PAGE_TARGNUM = 1;
	/**
	 * 需要显示的标签数
	 * @var int
	 */
	const DEFAULT_PAGE_TARGNUM = 10;
	/**
	 * 每页显示条数
	 * @var int
	 */
	const DEFAULT_PAGE_LIMIT = 10;
	
	private $_totalPages = self::DEFAULT_TOTAL_PAGE;
	
	private $_limit = self::DEFAULT_PAGE_LIMIT;
	
	private $_nowPage = self::DEFAULT_NOW_PAGE_TARGNUM;
	
	private $_pagetargnum = self::DEFAULT_PAGE_TARGNUM;
	private $_firstpage = '|<'; // 首页
	private $_uppage = '<'; // 上一页
	private $_nextpage = '>'; // 下一页
	private $_lastpage = '>|'; // 最后一页
	/**
	 * 构造函数，初始化分页标签相关属性值
	 * @param Object $cPagination
	 */
	public function __construct($cPagination) {
		$this->_totalPages = $cPagination->getPageCount();
		$this->_limit = $cPagination->getLimit();
		$this->_nowPage = $cPagination->getCurrentPage();
		$this->_pagetargnum = $cPagination->getPageTargNum();
	}	
	/**
	 * 创建分页连接标签
	 * @param array $param 分页时，需要带的一些参数以及连接地址，还有当前页的样式class
	 */
	function CreatePageLink($param = array()){
		$totalPages = $this->_totalPages;
		$nowPage = $this->_nowPage;
		$limit = $this->_limit;
		$tagnum = $this->_pagetargnum;
		
		$class = 'checkhere'; // 默认是后台样式 checkhere
		if(!empty($param)){
			if(isset($param['class'])){
				$class = $param['class'];
			}
			$link = $param['link'];
			$param = $param['param'];
		} else {
			$link = '';
			$param = '';
		}
		if(!empty($param)){
			$param = '&'.$param;
		}
		$pagehtml = '<div class="list_page">';
		$start = 0; // 标签开始的数字
		$max = 0; // 标签结束的数字
		
		if ($totalPages == 1 || ! $totalPages) {
			echo $pagehtml.'</div>';
		}
		$med = ceil ($tagnum / 2); // 分页变量调节器
		
		
		$start = $nowPage - $med;
		if ($start <= 0){
			$start = 1;
		}
		$max = $start + $tagnum;
		if ($max > $totalPages){
			$max = $totalPages + 1;
		}
		
		if ($start > 1){ // 增加首页、上一页标签
			$pagehtml .= '<a title="首页" href="'.$link.'?page=1'.$param.'">' . $this->_firstpage . '</a>';
			$up = $nowPage - 1;
			if ($up <= 0) {
				$up = 1;
			}
			$pagehtml .= '<a title="上一页" href="'.$link.'?page='.$up.$param.'">' . $this->_uppage . '</a>';
			$pagehtml .= '<a title="上一页" href="'.$link.'?page=1'.$param.'">1...</a>';
		}
		$next = max;
		if ($next > $totalPages){
			$next = $totalPages;
		}
		
		for($i = $start; $i < $max; $i ++){
			if ($i == $nowPage) { // 选择中当前页
				$pagehtml .= '<a title="第' . $i . '页" href="'.$link.'?page='.$i.$param.'" class="'.$class.'">' . $i . '</a>';
			} else {
				$pagehtml .= '<a title="第' . $i . '页" href="'.$link.'?page='.$i.$param.'">' . $i . '</a>';
			}
		}
		$m = $max - 1;
		if ($m < $totalPages){
			$next = $nowPage + 1;
			if ($next > $totalPages){
				$next = $totalPages;
			}
			if ($totalPages > $tagnum){
				$pagehtml .= '<a title="'.$this->_lastpage.'" href="'.$link.'?page='.$totalPages.$param.'">...' . $totalPages . '</a>';
			}
			$pagehtml .= '<a title="下一页" href="'.$link.'?page='.$next.$param.'">' . $this->_nextpage . '</a>';
			$pagehtml .= '<a title="尾页" href="'.$link.'?page='.$totalPages.$param.'">' . $this->_lastpage . '</a>';
		}
		$pagehtml .= '</div>';
		echo $pagehtml;
	
	}
}
