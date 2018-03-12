<?php

namespace forms\core;

class Font extends Collection {

    protected $name = '';
    protected $family = '';
    protected $url = '';

    public function getName(){
        return $this->name;
    }   

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    public function getFamily(){
        return $this->family;
    }   

    public function setFamily($family) {
        $this->family = $family;
        return $this;
    }
    public function getUrl(){
        return $this->url;
    }   

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public  function toHtml()
    {
        
        $html = new View();
        $html->setName($this->name);
        $html->render( 'webfont', 'templates' );
        
    }


}