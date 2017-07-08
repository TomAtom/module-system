<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class Authentification extends AbstractActionController {

  private $userManager;

  /**
   * @var \Zend\Authentication\AuthenticationService
   */
  private $authenticationService;

  public function __construct(\Zend\Authentication\AuthenticationService $authenticationService,
                              \System\Service\UserManager $userManager) {
    $this->authenticationService = $authenticationService;
    $this->userManager = $userManager;
  }

  public function getAuthService() {
    return $this->authenticationService;
  }

  public function loginAction() {
    $authService = $this->getAuthService();
    if ($authService->hasIdentity()) {
      return $this->redirect()->toRoute('home');
    }
    $form = $this->getLoginForm();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $form->setData($request->getPost());
      if ($form->isValid()) {
        $this->processLogin($request);
      }
    }
    return array('form' => $form);
  }

  private function processLogin(\Zend\Http\Request $request) {
    $authService = $this->getAuthService();
    $adapter = $authService->getAdapter();
    $adapter->setEmail($request->getPost('email'));
    $adapter->setPassword($request->getPost('password'));
    $result = $this->getAuthService()->authenticate();
    if ($result->isValid()) {
      $storage = $this->getAuthService()->getStorage();
      $identity = $this->getAuthService()->getAdapter()->getIdentityData();
      $storage->write($identity);
      $this->flashMessenger()->addSuccessMessage('Uživatel byl přihlášen');
      $returnUri = $request->getPost('return');
      if ($returnUri != '') {
        $this->redirect()->toUrl($returnUri);
      } else {
        $this->redirect()->toRoute('home');
      }
    } else {
      $this->flashMessenger()->addInfoMessage('Přihlášení se nezdařilo. Zadejte prosím platné přihlašovací údaje.');
    }
  }

  public function logoutAction() {
    $this->getAuthService()->clearIdentity();
    $this->flashmessenger()->addInfoMessage('Odhlášeno');
    return $this->redirect()->toRoute('authentification');
  }

  private function getLoginForm() {
    $form = new \System\Form\LoginForm();
    $returnUri = $this->params()->fromQuery('return');
    if ($returnUri) {
      $form->get('return')->setValue($returnUri);
    }
    return $form;
  }

}