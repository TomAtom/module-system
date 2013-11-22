<?php

namespace System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController {
    
    protected $userTable;

    public function getUserTable() {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('System\Model\UserTable');
        }
        return $this->userTable;
    }

    public function indexAction() {
        return new ViewModel(array(
            'users' => $this->getUserTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $sm = $this->getServiceLocator();
        $form = new \System\Form\UserForm();
        $form->get('submit')->setValue('Přidat');

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost('return')) {
                return $this->redirect()->toRoute('user');
            }
            $user = $sm->get('System\Model\User');
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                try {
                    $user->populate($user->sanitizeData($form->getData()));
                    $user->save();
                    $password = $form->get('password')->getValue();
                    $user->setPassword($password);
                    $user->save();
                    $this->flashMessenger()->addSuccessMessage('Uživatel přidán');
                    return $this->redirect()->toRoute('user');
                } catch (\System\Exception\AlreadyExistsException $e) {
                   $this->flashMessenger()->addErrorMessage('Uživatel s emailem "'.$user->email.'" již existuje');
                   return $this->redirect()->toRoute('user', array(
                       'action' => 'add'
                   ));
                }
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $idUser = (int) $this->params()->fromRoute('id', 0);
        if (!$idUser) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }
        $user = $this->getUserTable()->getUser($idUser);

        $sm = $this->getServiceLocator();
        $form = new \System\Form\UserForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Uložit');       

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost('return')) {
                return $this->redirect()->toRoute('user');
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
                    $form->getData()->save();
                    $password = $form->get('password')->getValue();
                    if ($password != '') {
                        $user->setPassword($password);
                        $user->save();
                    }
                    $this->flashMessenger()->addSuccessMessage('Uživatel uložen');
                    return $this->redirect()->toRoute('user');
                } catch (\System\Exception\AlreadyExistsException $e) {
                    $this->flashMessenger()->addErrorMessage('Uživatel s emailem "'.$user->email.'" již existuje');
                    return $this->redirect()->toRoute('user', array(
                        'action' => 'edit',
                        'id' => $idUser
                    ));
                }
            }
        }

        return array(
            'id' => $idUser,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $idUser = (int) $this->params()->fromRoute('id', 0);
        if (!$idUser) {
            return $this->redirect()->toRoute('user');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', array());

            if (array_key_exists('yes', $del)) {
                $id = (int) $request->getPost('id');
                $user = $this->getUserTable()->getUser($id);
                $user->delete();
                $this->flashMessenger()->addSuccessMessage('Uživatel smazán');
            }
            return $this->redirect()->toRoute('user');
        }

        return array(
            'user' => $this->getUserTable()->getUser($idUser)
        );
    }
    
    public function profileAction() {
        $sm = $this->getServiceLocator();
        $authenticationService = $sm->get('AuthentificationService');
        if (!$authenticationService->hasIdentity()) {
            throw new \Exception('uzivatel bez identity nemuze editovat svuj profil');
        }
        $identity = $authenticationService->getIdentity();
        $user = $this->getUserTable()->getUser($identity->id_user);
        $form = new \System\Form\UserForm();
        $form->remove('is_admin');
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
                    $form->getData()->save();
                    $password = $form->get('password')->getValue();
                    if ($password != '') {
                        $user->setPassword($password);
                        $user->save();
                    }
                    $this->flashMessenger()->addSuccessMessage('Uloženo');
                    return $this->redirect()->toRoute('user', array('action' => 'profile'));
                } catch (\System\Exception\AlreadyExistsException $e) {
                    $this->flashMessenger()->addErrorMessage('Uživatel s emailem "'.$user->email.'" již existuje');
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
        $idUser = (int) $this->params()->fromRoute('id', 0);
        $sm = $this->getServiceLocator();
        $roleTable = $sm->get('System\Model\RoleTable');
        $userRoleTable = $sm->get('System\Model\UserRoleTable');
        $form = new \System\Form\UserRoles('role');
        $form->setRolesTypes($roleTable->fetchAll());
        $form->setRolesIds($userRoleTable->getRolesIdsByUser($idUser));
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost('return')) {
                return $this->redirect()->toRoute('user');
            }
            $dataFromForm = $request->getPost('roles', array());
            $callback = function($var) {
                return $var == '1';
            };
            $rolesIds = array_keys(array_filter($dataFromForm, $callback));
            $userRoleTable->setUserRoles($idUser, $rolesIds);
            $this->flashMessenger()->addSuccessMessage('Uloženo');
            return $this->redirect()->toRoute('user');
        }
        return array(
            'form' => $form,
            'id' => $idUser
        );
    }

}