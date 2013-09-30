<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AuthentificationController extends AbstractActionController {
    
    protected $authservice;
	     
    public function getAuthService() {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()
                                      ->get('AuthService');
        }
        return $this->authservice;
    }
    
    public function loginAction() {
        $sm = $this->getServiceLocator();
        $authService = $sm->get('AuthService');
        if ($authService->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }
        $form = new \System\Form\LoginForm();
        $request = $this->getRequest();
        if ($request->isPost()){
            $form->setData($request->getPost());
            if ($form->isValid()){
                $this->getAuthService()->getAdapter()
                                        ->setIdentity($request->getPost('email'))
                                        ->setCredential($request->getPost('password'));
                $result = $this->getAuthService()->authenticate();
                if ($result->isValid()) {
                    $storage = $this->getAuthService()->getStorage();
                    $storage->write($this->getAuthService()->getAdapter()->getResultRowObject(
                        array('id_user', 'name', 'surname', 'email', 'last_login', 'id_role', 'is_admin'),
                        null
                    ));
                    $this->setUserLoginDateTime($this->getAuthService()->getIdentity()->id_user);
                    $this->flashMessenger()->addSuccessMessage('Uživatel byl přihlášen');
                    $this->redirect()->toRoute('home');
                } else {
                    $this->flashMessenger()->addInfoMessage('Přihlášení se nezdařilo. Zadejte prosím platné přihlašovací údaje.');
                    $this->redirect();
                }
            }
            
        }
        return array('form' => $form);
    }

    public function logoutAction()
    {
        $this->getAuthService()->clearIdentity();
        $this->flashmessenger()->addInfoMessage('Ohlášeno');
        return $this->redirect()->toRoute('authentification');
    }
    
    private function setUserLoginDateTime($idUser) {
        $userTable = $this->getServiceLocator()->get('System\Model\UserTable');
        $user = $userTable->getUser($idUser);
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();
    }

}
