<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AuthentificationController extends AbstractActionController {

  /**
   * @var \System\Model\UserTable
   */
  private $userTable;

  /**
   * @var \System\Model\UserRoleTable
   */
  private $userRoleTable;

  /**
   * @var \Zend\Authentication\AuthenticationService
   */
  private $authenticationService;

  public function __construct(\Zend\Authentication\AuthenticationService $authenticationService, \System\Model\UserRoleTable $userRoleTable, \System\Model\UserTable $userTable) {
    $this->authenticationService = $authenticationService;
    $this->userRoleTable = $userRoleTable;
    $this->userTable = $userTable;
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
    $this->getAuthService()->getAdapter()
            ->setIdentity($request->getPost('email'))
            ->setCredential($request->getPost('password'));
    $result = $this->getAuthService()->authenticate();
    if ($result->isValid()) {
      $storage = $this->getAuthService()->getStorage();
      $identity = $this->getAuthService()->getAdapter()->getResultRowObject(
              array('id_user', 'name', 'surname', 'email', 'last_login', 'id_role', 'is_admin', 'is_active'), null
      );
      if (!$identity->is_active) {
        $this->getAuthService()->clearIdentity();
        $this->flashMessenger()->addInfoMessage('Uživatel nemá povoleno přihlášení.');
        return $this->redirect('authentification');
      }
      $identity->rolesIds = $this->userRoleTable->getRolesIdsByUser($identity->id_user);
      $storage->write($identity);
      $this->setUserLoginDateTime($this->getAuthService()->getIdentity()->id_user);
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
    $this->flashmessenger()->addInfoMessage('Ohlášeno');
    return $this->redirect()->toRoute('authentification');
  }

  private function setUserLoginDateTime($idUser) {
    $user = $this->userTable->getUser($idUser);
    $user->last_login = date('Y-m-d H:i:s');
    $this->userTable->saveUser($user);
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
