<?php
class Pagination {
    public $page_current, $page_per, $item_count, $url;
    
    public function __construct($page_current=1, $page_per=20, $item_count=0){
        $this->item_count = (int) $item_count;
        $this->page_current = (int) $page_current;
        $this->page_per = (int) $page_per;
        $this->set_url();
    }
    
    public function set_url(){
        $input = $_SERVER['REQUEST_URI'];
        $pattern = '/&?page=\d*/';
        $temp = preg_replace($pattern, "", $input);
        $temp = is_numeric(strpos($temp, "?")) ? $temp : $temp . "?";
        $temp = (substr($temp, -1)=="?" || substr($temp, -1)=="&") ? $temp : $temp . "&";
        $this->url = $temp;
    }
    
    public function offset() {
        return $this->page_per * ($this->page_current - 1);
    }
    
    public function total_pages(){
        return ceil($this->item_count / $this->page_per);
    }
    
    public function previous_page() {
        $prev = $this->page_current - 1;
        return ($prev > 0) ? $prev : false;
    }
    
    public function next_page() {
        $next = $this->page_current + 1;
        return ($next <= $this->total_pages()) ? $next : false;
    }
    
    public function previous_link() {
        $link = "";
        $disabled = $this->previous_page() == false ? " disabled" : "";
        $link .= '<li class="page-item' . $disabled . '">';
        $link .= "<a href=\"{$this->url}page={$this->previous_page()}\" class=\"page-link\">";
        $link .= "&laquo; Previous</a>";
        $link .= '</li>';
        
        return $link;
    }
    
    public function next_link($url="") {
        $link = "";
        $disabled = $this->next_page() == false ? " disabled" : "";
        $link .= '<li class="page-item' .  $disabled .'">';
        $link .= "<a href=\"{$this->url}page={$this->next_page()}\" class=\"page-link\">";
        $link .= "next &raquo;</a>";
        $link .= '</li>';
        
        return $link;
    }
    
    public function number_links($url="") {
        $output = "";
        for($i=1; $i <= $this->total_pages(); $i++){
            if($i == $this->page_current){
                $output .= '<li class="page-item active">';
                $output .= "<span class=\"page-link\">{$i}</span>";
                $output .= '</li>';
            } else {
                $output .= '<li class="page-item">';
                $output .= "<a href=\"{$this->url}page={$i}\" class=\"page-link\">{$i}</a>";
                $output .= '</li>';
            }
        }
        return $output;
    }
    
    public function page_links($url) {
        $output = "";
        if($this->total_pages() > 1){
            $output .= "<ul class=\"pagination d-flex justify-content-center\">";
            $output .= $this->previous_link($url);
            $output .= $this->number_links($url);
            $output .= $this->next_link($url);
            $output .= "</ul>";
        }
        return $output;
    }
}