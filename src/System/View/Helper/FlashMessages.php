<?php

namespace System\View\Helper;  
use Zend\View\Helper\AbstractHelper;

class FlashMessages extends AbstractHelper
{   
    protected  $flashMessenger;
    
    public function __construct(\Zend\Mvc\Controller\Plugin\FlashMessenger $messenger) {
        $this->flashMessenger = $messenger;
    }

    public function __invoke() {
        $output = null;
        
        if ($this->flashMessenger->hasMessages()) {
            $output .= '<ul id="messages">';
            foreach ($this->flashMessenger->getMessages() as $message) {
                if (is_array($message)) {
                    $output .= '<li class="' . key($message) . '">' . current($message) . '</li>';
                } else {
                    $output .= '<li>'.$message.'</li>';
                }
            }
            $output .= '</ul>';
        }
       
        return $output;
    }
    
}  