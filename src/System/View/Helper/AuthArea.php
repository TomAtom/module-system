<?php

namespace System\View\Helper;  
use Zend\View\Helper\AbstractHelper;

class AuthArea extends AbstractHelper
{   
    
    protected $authService = null;


    public function __construct(\Zend\Authentication\AuthenticationService $authService) {
        $this->authService = $authService;
    }

    public function __invoke() {
        $html = null;
        $urlHelper = $this->getView()->plugin('url');
        if ($this->authService->hasIdentity()) {
            $html .= '<a href="'.$urlHelper('authentification', array('action' => 'logout')).'">Odhlásit</a>';
            $identity = $this->authService->getIdentity();
            $html .= '&nbsp'.$identity->name.'&nbsp'.$identity->surname;
        } else {
            $html .= '<a href="'.$urlHelper('authentification', array('action' => 'login')).'">Přihlásit</a>';
        }
        return $html;
    }
    
}  
