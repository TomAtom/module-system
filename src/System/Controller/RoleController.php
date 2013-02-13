<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use System\Model\Role;


class RoleController extends AbstractActionController {
    
    protected $roleTable;

    public function getRoleTable() {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('System\Model\RoleTable');
        }
        return $this->roleTable;
    }

    public function indexAction() {
        return array(
            'roles' => $this->getRoleTable()->fetchAll(),
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
                    $this->getRoleTable()->saveRole($role);
                    $this->flashMessenger()->addMessage(array('message' => 'Role přidána'));
                    return $this->redirect()->toRoute('role');
                } catch (\System\Exception\AlreadyExistsException $e) {
                    $this->flashMessenger() ->addMessage(array('warning' => 'Role s názvem "'.$role->name.'" již existuje'));
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
        $role = $this->getRoleTable()->getRole($id);

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
                    $this->getRoleTable()->saveRole($form->getData());
                    $this->flashMessenger()->addMessage(array('message' => 'Role uložena'));
                    return $this->redirect()->toRoute('role');
                } catch (\System\Exception\AlreadyExistsException $e) {
                    $this->flashMessenger() ->addMessage(array('warning' => 'Role s názvem "'.$role->name.'" již existuje'));
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

    public function deleteAction()
    {
        $idRole = (int) $this->params()->fromRoute('id', 0);
        if (!$idRole) {
            return $this->redirect()->toRoute('role');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', array());

            if (array_key_exists('yes', $del)) {
                $id = (int) $request->getPost('id');
                $this->getRoleTable()->deleteRole($id);
                $this->flashMessenger()->addMessage(array('message' => 'Role smazána'));
            }
            return $this->redirect()->toRoute('role');
        }

        return array(
            'role' => $this->getRoleTable()->getRole($idRole)
        );
    }
    
    public function rightsAction() {
        $idRole = (int) $this->params()->fromRoute('id', 0);
        $sm = $this->getServiceLocator();
        $rightTable = $sm->get('System\Model\RightTable');
        $form = new \System\Form\RightsForm();
        $config = $sm->get('Config');
        $form->setControllers($config['controllers']['invokables']);
        $form->setRights($rightTable->getRightsByRole($idRole));
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost('return')) {
                return $this->redirect()->toRoute('role');
            }
            $rightTable->getAdapter()->getDriver()->getConnection()->beginTransaction();
            $rightTable->deleteRightsByRole($idRole);
            $rights = $request->getPost('rights', array());
            foreach ($rights as $controller => $actions) {
                foreach ($actions as $action => $actionChecked) {
                    if ($actionChecked) {
                        $right = new \System\Model\Right();
                        $right->exchangeArray(array('id_role' => $idRole,
                                                   'controller' => $controller,
                                                   'action' => $action));
                        $rightTable->addRight($right);
                    }
                }
            }
            $rightTable->getAdapter()->getDriver()->getConnection()->commit();
            $this->flashMessenger()->addMessage(array('message' => 'Uloženo'));
            return $this->redirect()->toRoute('role');
        }
        return array(
            'id' => $idRole,
            'form' => $form
        );
    }

}
