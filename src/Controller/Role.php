<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class Role extends AbstractActionController {

  /**
   * @var \System\Service\RoleManager
   */
  private $roleManager;

  /**
   * @var \Doctrine\ORM\EntityManager
   */
  private $entityManager;

  /**
   * @var \Zend\Cache\Storage\FlushableInterface
   */
  private $aclCache;

  /**
   * @var \System\Form\RightsForm
   */
  private $rightsForm;

  public function __construct(\Doctrine\ORM\EntityManager $entityManager, \System\Service\RoleManager $roleManager,
                              \System\Form\RightsForm $rightsForm, \Zend\Cache\Storage\FlushableInterface $aclCache) {
    $this->rightsForm = $rightsForm;
    $this->aclCache = $aclCache;
    $this->entityManager = $entityManager;
    $this->roleManager = $roleManager;
  }

  public function indexAction() {
    return array(
      'roles' => $this->entityManager->getRepository(\System\Entity\Role::class)->findAll(),
    );
  }

  public function addAction() {
    $form = new \System\Form\RoleForm();
    $form->get('submit')->setValue('Přidat');

    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        return $this->redirect()->toRoute('role');
      }
      $role = new \System\Entity\Role();
      $form->bind($role);
      $form->setInputFilter($form->getInputFilter());
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $this->roleManager->add($role);
          $this->flashMessenger()->addSuccessMessage('Role přidána');
          return $this->redirect()->toRoute('role');
        } catch (\System\Exception\AlreadyExistsException $e) {
          $this->flashMessenger()->addErrorMessage('Role s názvem "' . $role->name . '" již existuje');
          return $this->redirect()->toRoute('role', array(
                    'action' => 'add'
          ));
        }
      }
    }
    return array('form' => $form);
  }

  public function editAction() {
    $id = (int) $this->params()->fromRoute('id', 0);
    if (!$id) {
      return $this->redirect()->toRoute('role', array(
                'action' => 'add'
      ));
    }
    $role = $this->entityManager->getRepository(\System\Entity\Role::class)->find($id);

    $form = new \System\Form\RoleForm();
    $form->bind($role);
    $form->get('submit')->setAttribute('value', 'Uložit');

    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        return $this->redirect()->toRoute('role');
      }
      $inputFilter = $form->getInputFilter();
      $form->setInputFilter($inputFilter);
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $this->roleManager->update($role);
          $this->flashMessenger()->addSuccessMessage('Role uložena');
          return $this->redirect()->toRoute('role');
        } catch (\System\Exception\AlreadyExistsException $e) {
          $this->flashMessenger()->addErrorMessage('Role s názvem "' . $role->name . '" již existuje');
          return $this->redirect()->toRoute('role',
                          array(
                            'action' => 'edit',
                            'id' => $id
          ));
        }
      }
    }

    return array(
      'id' => $id,
      'form' => $form,
    );
  }

  public function deleteAction() {
    $idRole = (int) $this->params()->fromRoute('id', 0);
    if (!$idRole) {
      return $this->redirect()->toRoute('role');
    }
    $role = $this->entityManager->getRepository(\System\Entity\Role::class)->find($idRole);

    $request = $this->getRequest();
    if ($request->isPost()) {
      $del = $request->getPost('del', array());

      if (array_key_exists('yes', $del)) {
        $id = (int) $request->getPost('id');
        $this->roleManager->delete($role);
        $this->flashMessenger()->addSuccessMessage('Role smazána');
      }
      return $this->redirect()->toRoute('role');
    }

    return array(
      'role' => $role
    );
  }

  public function rightsAction() {
    $idRole = (int) $this->params()->fromRoute('id', 0);
    $role = $this->entityManager->getRepository(\System\Entity\Role::class)->find($idRole);
    $this->rightsForm->setRights($role->getRights());
    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        return $this->redirect()->toRoute('role');
      }
      $rights = $request->getPost('rights', array());
      $this->roleManager->updateRights($role, $rights);
      $this->aclCache->flush();
      $this->flashMessenger()->addSuccessMessage('Uloženo');
      return $this->redirect()->toRoute('role');
    }
    return array(
      'id' => $idRole,
      'form' => $this->rightsForm
    );
  }

}