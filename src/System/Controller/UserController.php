<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController {

  /**
   * @var \System\Model\UserRoleTable
   */
  private $userRoleTable;

  /**
   * @var \System\Model\RoleTable
   */
  private $roleTable;

  /**
   * @var \Zend\Authentication\AuthenticationService
   */
  private $authService;

  /**
   * @var System\Model\UserTable
   */
  private $userTable;

  public function __construct(\System\Model\UserTable $userTable, \Zend\Authentication\AuthenticationService $authService, \System\Model\RoleTable $roleTable, \System\Model\UserRoleTable $userRoleTable) {
    $this->userTable = $userTable;
    $this->authService = $authService;
    $this->roleTable = $roleTable;
    $this->userRoleTable = $userRoleTable;
  }

  private function getUserTable() {
    return $this->userTable;
  }

  private function getUserForm() {
    return new \System\Form\UserForm();
  }

  public function indexAction() {
    $page = $this->params()->fromRoute('page');
    $filterFormPrefix = 'userFilter';
    $form = new \System\Form\UserSearchForm($filterFormPrefix);
    $requestParams = $this->params()->fromQuery();
    $userQuery = $this->getUserTable()->getGateway()->getSql()->select()->where;

    if (isset($requestParams[$filterFormPrefix]['name']) && $requestParams[$filterFormPrefix]['name'] != '') {
      $userQuery->like('name', '%' . $requestParams[$filterFormPrefix]['name'] . '%');
    }

    if (isset($requestParams[$filterFormPrefix]['surname']) && $requestParams[$filterFormPrefix]['surname'] != '') {
      $userQuery->like('surname', '%' . $requestParams[$filterFormPrefix]['surname'] . '%');
    }

    if (isset($requestParams[$filterFormPrefix]['email']) && $requestParams[$filterFormPrefix]['email'] != '') {
      $userQuery->like('email', '%' . $requestParams[$filterFormPrefix]['email'] . '%');
    }

    $paginatorAdapter = new \Zend\Paginator\Adapter\DbTableGateway($this->getUserTable()->getGateway(), $userQuery);
    $paginator = new \Zend\Paginator\Paginator($paginatorAdapter);
    $paginator->setCurrentPageNumber($page);
    $paginator->setDefaultItemCountPerPage(10);

    if (array_key_exists($filterFormPrefix, $requestParams)) {
      $form->populateValues($requestParams[$filterFormPrefix]);
    }

    return array(
        'users' => $paginator,
        'requestParams' => $requestParams,
        'form' => $form
    );
  }

  public function addAction() {
    $form = $this->getUserForm();
    $form->get('submit')->setValue('Přidat');

    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        return $this->redirect()->toRoute('user');
      }
      $user = new \System\Model\User();
      $form->setInputFilter($user->getInputFilter());
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $user->exchangeArray($form->getData());
          $this->getUserTable()->saveUser($user);
          $password = $form->get('password')->getValue();
          $this->getUserTable()->setPassword($user->id_user, $password);
          $this->flashMessenger()->addSuccessMessage('Uživatel přidán');
          return $this->redirect()->toRoute('user');
        } catch (\System\Exception\AlreadyExistsException $e) {
          $this->flashMessenger()->addErrorMessage('Uživatel s emailem "' . $user->email . '" již existuje');
          return $this->redirect()->toRoute('user', array(
                      'action' => 'add'
          ));
        }
      }
    }
    return array('form' => $form);
  }

  public function editAction() {
    $page = $this->params()->fromRoute('page');
    $requestParams = $this->params()->fromQuery();
    $idUser = (int) $this->params()->fromRoute('id', 0);
    if (!$idUser) {
      return $this->redirect()->toRoute('user', array(
                  'action' => 'add'
      ));
    }
    $user = $this->getUserTable()->getUser($idUser);

    $form = $this->getUserForm();
    $form->bind($user);
    $form->get('submit')->setAttribute('value', 'Uložit');

    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        $url = $this->url()->fromRoute('user', array('page' => $page));
        return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
      }
      $inputFilter = $user->getInputFilter();
      $inputFilter->get('password')->setRequired(false);
      if (!$request->getPost('password')) {
        $inputFilter->get('password2')->setRequired(false);
      }
      $form->setInputFilter($inputFilter);
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $this->getUserTable()->saveUser($user);
          $password = $form->get('password')->getValue();
          if ($password != '') {
            $this->getUserTable()->setPassword($user->id_user, $password);
          }
          $this->flashMessenger()->addSuccessMessage('Uživatel uložen');
          $url = $this->url()->fromRoute('user', array('page' => $page));
          return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
        } catch (\System\Exception\AlreadyExistsException $e) {
          $this->flashMessenger()->addErrorMessage('Uživatel s emailem "' . $user->email . '" již existuje');
          $url = $this->url()->fromRoute('user', array(
              'action' => 'edit',
              'id' => $idUser,
              'page' => $page
          ));
          return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
        }
      }
    }

    return array(
        'id' => $idUser,
        'form' => $form,
        'page' => $page,
        'requestParams' => $requestParams,
    );
  }

  public function deleteAction() {
    $page = $this->params()->fromRoute('page');
    $requestParams = $this->params()->fromQuery();
    $idUser = (int) $this->params()->fromRoute('id', 0);
    if (!$idUser) {
      return $this->redirect()->toRoute('user');
    }

    $request = $this->getRequest();
    if ($request->isPost()) {
      $del = $request->getPost('del', array());

      if (array_key_exists('yes', $del)) {
        $id = (int) $request->getPost('id');
        $this->getUserTable()->deleteUser($id);
        $this->flashMessenger()->addSuccessMessage('Uživatel smazán');
      }
      $url = $this->url()->fromRoute('user', array('page' => $page));
      return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
    }

    return array(
        'user' => $this->getUserTable()->getUser($idUser),
        'page' => $page,
        'requestParams' => $requestParams,
    );
  }

  public function profileAction() {
    if (!$this->authService->hasIdentity()) {
      throw new \Exception('uzivatel bez identity nemuze editovat svuj profil');
    }
    $identity = $this->authService->getIdentity();
    $user = $this->getUserTable()->getUser($identity->id_user);
    $form = $this->getUserForm();
    $form->remove('is_admin');
    $form->remove('is_active');
    $form->bind($user);
    $form->get('submit')->setAttribute('value', 'Uložit');

    $request = $this->getRequest();
    if ($request->isPost()) {
      $inputFilter = $user->getInputFilter();
      $inputFilter->get('password')->setRequired(false);
      if (!$request->getPost('password')) {
        $inputFilter->get('password2')->setRequired(false);
      }
      $form->setInputFilter($inputFilter);
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $this->getUserTable()->saveUser($user);
          $password = $form->get('password')->getValue();
          if ($password != '') {
            $this->getUserTable()->setPassword($user->id_user, $password);
          }
          $this->flashMessenger()->addSuccessMessage('Uloženo');
          return $this->redirect()->toRoute('user', array('action' => 'profile'));
        } catch (\System\Exception\AlreadyExistsException $e) {
          $this->flashMessenger()->addErrorMessage('Uživatel s emailem "' . $user->email . '" již existuje');
          return $this->redirect()->toRoute('user', array(
                      'action' => 'profile',
          ));
        }
      }
    }

    return array(
        'form' => $form,
    );
  }

  public function roleAction() {
    $page = $this->params()->fromRoute('page');
    $requestParams = $this->params()->fromQuery();
    $idUser = (int) $this->params()->fromRoute('id', 0);
    $form = new \System\Form\UserRoles('role');
    $form->setRolesTypes($this->roleTable->fetchAll());
    $form->setRolesIds($this->userRoleTable->getRolesIdsByUser($idUser));
    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        $url = $this->url()->fromRoute('user', array('page' => $page));
        return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
      }
      $dataFromForm = $request->getPost('roles', array());
      $callback = function($var) {
        return $var == '1';
      };
      $rolesIds = array_keys(array_filter($dataFromForm, $callback));
      $userRoleTable->setUserRoles($idUser, $rolesIds);
      $this->flashMessenger()->addSuccessMessage('Uloženo');
      $url = $this->url()->fromRoute('user', array('page' => $page));
      return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
    }
    return array(
        'form' => $form,
        'id' => $idUser,
        'page' => $page,
        'requestParams' => $requestParams,
    );
  }

}
