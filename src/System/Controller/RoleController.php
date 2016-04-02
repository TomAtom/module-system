<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use System\Model\Role;

class RoleController extends AbstractActionController {

  /**
   * @var \System\Model\RightTable
   */
  private $rightTable;

  /**
   * @var \System\Model\RoleTable
   */
  private $roleTable;

  /**
   * @var \Zend\Cache\Storage\FlushableInterface
   */
  private $aclCache;

  /**
   * @var \System\Form\RightsForm
   */
  private $rightsForm;

  public function __construct(\System\Model\RoleTable $roleTable, \System\Form\RightsForm $rightsForm, \Zend\Cache\Storage\FlushableInterface $aclCache, \System\Model\RightTable $rightTable) {
    $this->rightsForm = $rightsForm;
    $this->aclCache = $aclCache;
    $this->roleTable = $roleTable;
    $this->rightTable = $rightTable;
  }

  public function indexAction() {
    return array(
        'roles' => $this->roleTable->fetchAll(),
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
      $role = new Role();
      $form->setInputFilter($role->getInputFilter());
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $role->exchangeArray($form->getData());
          $this->roleTable->saveRole($role);
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
    $role = $this->roleTable->getRole($id);

    $form = new \System\Form\RoleForm();
    $form->bind($role);
    $form->get('submit')->setAttribute('value', 'Uložit');

    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        return $this->redirect()->toRoute('role');
      }
      $inputFilter = $role->getInputFilter();
      $form->setInputFilter($inputFilter);
      $form->setData($request->getPost());

      if ($form->isValid()) {
        try {
          $this->roleTable->saveRole($form->getData());
          $this->flashMessenger()->addSuccessMessage('Role uložena');
          return $this->redirect()->toRoute('role');
        } catch (\System\Exception\AlreadyExistsException $e) {
          $this->flashMessenger()->addErrorMessage('Role s názvem "' . $role->name . '" již existuje');
          return $this->redirect()->toRoute('role', array(
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

    $request = $this->getRequest();
    if ($request->isPost()) {
      $del = $request->getPost('del', array());

      if (array_key_exists('yes', $del)) {
        $id = (int) $request->getPost('id');
        $this->roleTable->deleteRole($id);
        $this->flashMessenger()->addSuccessMessage('Role smazána');
      }
      return $this->redirect()->toRoute('role');
    }

    return array(
        'role' => $this->roleTable->getRole($idRole)
    );
  }

  public function rightsAction() {
    $idRole = (int) $this->params()->fromRoute('id', 0);
    $this->rightsForm->setRights($this->rightTable->getRightsByRole($idRole));
    $request = $this->getRequest();
    if ($request->isPost()) {
      if ($request->getPost('return')) {
        return $this->redirect()->toRoute('role');
      }
      $this->rightTable->getAdapter()->getDriver()->getConnection()->beginTransaction();
      $this->rightTable->deleteRightsByRole($idRole);
      $rights = $request->getPost('rights', array());
      foreach ($rights as $controller => $actions) {
        foreach ($actions as $action => $actionChecked) {
          if ($actionChecked) {
            $right = new \System\Model\Right();
            $right->exchangeArray(array('id_role' => $idRole,
                'controller' => $controller,
                'action' => $action));
            $this->rightTable->addRight($right);
          }
        }
      }
      $this->rightTable->getAdapter()->getDriver()->getConnection()->commit();
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
