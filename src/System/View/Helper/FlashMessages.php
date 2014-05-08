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
        foreach ($this->flashMessenger->getMessages() as $message) {
            $output .= '<div class="alert">'.$message.'</div>';
        }
        foreach ($this->flashMessenger->getSuccessMessages() as $message) {
            $output .= '<div class="alert alert-success">'.$message.'</div>';
        }
        foreach ($this->flashMessenger->getErrorMessages() as $message) {
            $output .= '<div class="alert alert-danger">'.$message.'</div>';
        }
        foreach ($this->flashMessenger->getWarningMessages() as $message) {
            $output .= '<div class="alert alert-warning">'.$message.'</div>';
        }
        foreach ($this->flashMessenger->getInfoMessages() as $message) {
            $output .= '<div class="alert alert-info">'.$message.'</div>';
        }
        return $output;
    }
    
}  