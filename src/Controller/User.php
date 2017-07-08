<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class User extends AbstractActionController {

  /**
   * @var \System\Service\UserManager
   */
  private $userManager;

  /**
   * @var \Doctrine\ORM\EntityManager
   */
  private $entityManager;

  /**
   * @var \Zend\Authentication\AuthenticationService
   */
  private $authService;

  public function __construct(\Doctrine\ORM\EntityManager $entityManager, \System\Service\UserManager $userManager,
                              \Zend\Authentication\AuthenticationService $authService) {
    $this->authService = $authService;
    $this->entityManager = $entityManager;
    $this->userManager = $userManager;
  }

  private function getUserForm() {
    return new \System\Form\UserForm();
  }

  public function indexAction() {
    $page = $this->params()->fromRoute('page');
    $queryBuilder = $this->entityManager->getRepository(\System\Entity\User::class)->createQueryBuilder('u');

    $filterFormPrefix = 'userFilter';
    $form = new \System\Form\UserSearchForm($filterFormPrefix);
    $requestParams = $this->params()->fromQuery();

    if (isset($requestParams[$filterFormPrefix]['name']) && $requestParams[$filterFormPrefix]['name'] != '') {
      $queryBuilder->where('u.name LIKE :name');
      $queryBuilder->setParameter('name', '%' . $requestParams[$filterFormPrefix]['name'] . '%');
    }

    if (isset($requestParams[$filterFormPrefix]['surname']) && $requestParams[$filterFormPrefix]['surname'] != '') {
      $queryBuilder->where('u.surname LIKE :surname');
      $queryBuilder->setParameter('surname', '%' . $requestParams[$filterFormPrefix]['surname'] . '%');
    }

    if (isset($requestParams[$filterFormPrefix]['email']) && $requestParams[$filterFormPrefix]['email'] != '') {
      $queryBuilder->where('u.email LIKE :email');
      $queryBuilder->setParameter('email', '%' . $requestParams[$filterFormPrefix]['email'] . '%');
    }

    if (array_key_exists($filterFormPrefix, $requestParams)) {
      $form->populateValues($requestParams[$filterFormPrefix]);
    }

    $adapter = new DoctrineAdapter(new ORMPaginator($queryBuilder->getQuery(), false));
    $paginator = new \Zend\Paginator\Paginator($adapter);

    $paginator->setDefaultItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);

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
      $user = new \System\Entity\User();
      $form->bind($user);
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $password = $form->get('password')->getValue();
          $user->setPassword($password);
          $this->userManager->add($user);
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
    $user = $this->entityManager->getRepository(\System\Entity\User::class)->find($idUser);

    $form = $this->getUserForm();
    $form->bind($user);
    $form->get('submit')->setAttribute('value', 'Uložit');

    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        $url = $this->url()->fromRoute('user', array('page' => $page));
        return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
      }
      $inputFilter = $form->getInputFilter();
      $inputFilter->get('password')->setRequired(false);
      if (!$request->getPost('password')) {
        $inputFilter->get('password2')->setRequired(false);
      }
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $this->userManager->update($user);
          $password = $form->get('password')->getValue();
          if ($password != '') {
            $this->userManager->setPassword($user, $password);
          }
          $this->flashMessenger()->addSuccessMessage('Uživatel uložen');
          $url = $this->url()->fromRoute('user', array('page' => $page));
          return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
        } catch (\System\Exception\AlreadyExistsException $e) {
          $this->flashMessenger()->addErrorMessage('Uživatel s emailem "' . $user->email . '" již existuje');
          $url = $this->url()->fromRoute('user',
                  array(
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
    $user = $this->entityManager->getRepository(\System\Entity\User::class)->find($idUser);
    if (!$user) {
      return $this->redirect()->toRoute('user');
    }

    $request = $this->getRequest();
    if ($request->isPost()) {
      $del = $request->getPost('del', array());
      if (array_key_exists('yes', $del)) {
        $this->userManager->delete($user);
        $this->flashMessenger()->addSuccessMessage('Uživatel smazán');
      }
      $url = $this->url()->fromRoute('user', array('page' => $page));
      return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
    }

    return array(
      'user' => $this->entityManager->getRepository(\System\Entity\User::class)->find($idUser),
      'page' => $page,
      'requestParams' => $requestParams,
    );
  }

  public function profileAction() {
    if (!$this->authService->hasIdentity()) {
      throw new \Exception('uzivatel bez identity nemuze editovat svuj profil');
    }
    $identity = $this->authService->getIdentity();
    $user = $this->entityManager->getRepository(\System\Entity\User::class)->find($identity->id_user);
    $form = $this->getUserForm();
    $form->remove('is_admin');
    $form->remove('is_active');
    $form->bind($user);
    $form->get('submit')->setAttribute('value', 'Uložit');

    $request = $this->getRequest();
    if ($request->isPost()) {
      $inputFilter = $form->getInputFilter();
      $inputFilter->get('is_admin')->setRequired(false);
      $inputFilter->get('is_active')->setRequired(false);
      $inputFilter->get('password')->setRequired(false);
      if (!$request->getPost('password')) {
        $inputFilter->get('password2')->setRequired(false);
      }
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $this->userManager->update($user);
          $password = $form->get('password')->getValue();
          if ($password != '') {
            $this->userManager->setPassword($user, $password);
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
    $user = $this->entityManager->getRepository(\System\Entity\User::class)->find($idUser);
    if (!\is_object($user)) {
      throw new Exception('nelze nalezt uzivatele s id ' . $idUser);
    }
    $form = new \System\Form\UserRoles('role');
    $form->setRolesTypes($this->entityManager->getRepository(\System\Entity\Role::class)->findAll());
    $form->setRoles($user->getRoles()->toArray());
    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        $url = $this->url()->fromRoute('user', array('page' => $page));
        return $this->redirect()->toUrl($url . '?' . http_build_query($requestParams));
      }
      $dataFromForm = $request->getPost('roles', array());
      $rolesIds = array_keys(array_filter($dataFromForm,
                      function($var) {
                return $var == '1';
              }));
      $this->userManager->assignRoles($user, $rolesIds);
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