<?php
class Qubit_Paginator_Adapter_Array{
	public $limit;
	public $offset;
	public $totalSql;
	private $page;
	private $pagtotal;
	public $_pagPrev;
	public $_pagNext;
    private $_ItemCountPerPage = 5;
    private $_PageRange = 5;
    private $_data;
    
    /**
     * [setPageRange description]
     * @param [type] $range [integer rango de paginas a mostrar en el paginator control]
     */
    public function setPageRange($range){
        $this->_PageRange = (int)$range;
        return $this;
    }
    /*
     * @param integer
     */
    public function setItemCountPerPage($count){
         $this->_ItemCountPerPage = (int) $count;    
         return $this;
    }
    
    public function getItems(){
    	return array_slice($this->_data, $this->offset, $this->limit);
    }

    public function setCurrentPageNumber($page){
         $this->page = (int) $page;    
         return $this;
    }
    
    public function assemble(){
        $this->pagtotal = ceil($this->totalSql/$this->_ItemCountPerPage);
        $this->limit = $this->_ItemCountPerPage;
        $this->offset = (($this->page-1)*$this->_ItemCountPerPage);
        $this->_pagPrev = $this->page-1;
        $this->_pagNext = $this->page+1;
        
        return $this;
    }

    public function getItemCount(){
        return $this->totalSql;
    }
    
    function __construct($data){
	    $this->totalSql = count($data);
	    $this->_data = $data;
	}
	
    public function __toString(){
        try {
            $sql = $this->_render();
        } catch (Qubit_Exception $e) {
            $sql = '';
        }

        return $sql;
    }
    
	public function paginas_en_nav($val){
            $this->pgMaximo = $val;
	}
	
	public function esPar($numero){ 
	    $resto = $numero%2; 
	    if (($resto==0) && ($numero!=0)) { 
	        return true; 
	    }
	    else{ 
	        return false; 
	    } 
	}
	
	//public function url_param($url_param){
        //    $this->url_parametros = $url_param;
	//}
	
    private function file_get_conten($file){
        ob_start();
        if (!include_once($file)){
            return false;
        }
        else{
            $salida = ob_get_contents(); 
        }
        ob_end_clean(); 
        return $salida; 
    }
    
    public function url($link = array(),$params = array()){
        
        $url = Qubit_Router::getInstance()->assemble($link ,$params);
        return $url;
   	}
    
	public function _render(){
            
	    if (!$this->pagtotal > 1 ){
	        return;
	    }
	    
	    if ($this->esPar($this->_PageRange)){
	        $this->_PageRange++;  
	    }
	    
	    $this->pgIntervalo = ($this->_PageRange-1)/2; 

	    $this->pg=$this->page-($this->pgIntervalo);
	    $this->i=0;

	    if ($this->page == $this->pagtotal){
	        $this->pg=$this->pg-2;
	    }
	    elseif (($this->page+$this->pgIntervalo) > $this->pagtotal){
	    	$this->pg=$this->pg-1;
	    }
	    
	    $salida = $this->file_get_conten(PATH_APP . 'application/views/script/paginator.php');
	    
	    return $salida ;
	}
}

?>