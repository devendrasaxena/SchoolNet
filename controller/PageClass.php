<?php 

class Pagination{
    
    public $_total = 1;
   // public $_limit = 50;
	public $_limit = 2;//PAGINATION_LIMIT;
    public $_page = 1;
	public $_base_link = '';
	public $_req_params = array();
	public $param = '';
    
    public $_db_start = 0;
    
    public function __construct($_page, $_limit){
        $this->_page = $_page;
        $this->_limit = $_limit;
		
        
        $this->_db_start =  ($_page - 1) * $_limit ;
        if( $this->_db_start < 0){
            $this->_db_start = 0;
        }
    }
    

    public function createLinks($param, $links, $list_class ) {
		  $this->param = $param; 
        if ( $this->_limit == 'all' || $this->_total <= $this->_limit ) {
            return '';
        }
		
		$base_link = '?';
		if( count($this->_req_params) ){
			$query_params= http_build_query($this->_req_params);
			$base_link = '?'. $query_params .'&';
		}

        $last       = ceil( $this->_total / $this->_limit );

        $start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
        $end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

        $html       = '<ul class="' . $list_class . '">';

        $class      = ( $this->_page == 1 ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="'.$base_link.$this->param.'&limit=' . $this->_limit . '&page=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';

        if ( $start > 1 ) {
            $html   .= '<li><a href="'.$base_link.$this->param.'limit=' . $this->_limit . '&page=1">1</a></li>';
            $html   .= '<li class="disabled"><span>...</span></li>';
        }

        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $this->_page == $i ) ? "active" : "";
            $html   .= '<li class="' . $class . '"><a href="'.$base_link.$this->param.'limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
        }

        if ( $end < $last ) {
            $html   .= '<li class="disabled"><span>...</span></li>';
            $html   .= '<li><a href="'.$base_link.$this->param.'limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>';
        }

        $class      = ( $this->_page == $last ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="'.$base_link.$this->param.'limit=' . $this->_limit . '&page=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';

        $html       .= '</ul>';

        return $html;
    }
    
}