<?php

namespace System\View\Helper;  
use Zend\View\Helper\AbstractHelper;

class AbsoluteUrl extends AbstractHelper {   

    protected $request;
 
    public function __construct(\Zend\Http\PhpEnvironment\Request $request) {
        $this->request = $request;
    }
 
    public function __invoke() {
        return $this->request->getUri()->normalize();
    }
    
}