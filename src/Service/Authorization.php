<?php

namespace System\Service;

class Authorization {

  private $config;

  const MODULES = [
    'Application',
    'System',
    'Guestbook',
    'Photogallery',
    'Article',
    'Discuss'
  ];

  protected $authentificationService;
  protected $acl;

  public function __construct(\System\Acl $acl, \Zend\Authentication\AuthenticationService $authentificationService, array $config) {
    $this->acl = $acl;
    $this->authentificationService = $authentificationService;
    $this->config = $config;
  }

  public function doAuthorization(\Zend\Mvc\MvcEvent $e) : void {
    if ($this->isControlerUnderAuthorizationControl($e->getRouteMatch()->getParam('controller'))) {
      if (!$this->isCurrentUserAllowed($e->getRouteMatch()->getParam('controller'),
                      $e->getRouteMatch()->getParam('action'))) {
        if ($this->existsControllerAction($e)) {
          if ($this->authentificationService->hasIdentity()) {
            $response = $e->getResponse();
            $response->setStatusCode(403);
            $vm = $e->getViewModel();
            $vm->setTemplate('layout/system/unauthorizedAccess');
            $e->stopPropagation();
          } else {
            $returnUri = $e->getRequest()->getUriString();
            $url = $e->getRouter()->assemble(array(), array('name' => 'authentification'));
            $response = $e->getResponse();
            $response->setStatusCode(302);
            $response->getHeaders()->addHeaderLine('Location', $url . '?return=' . \urlencode($returnUri));
            $e->stopPropagation();
          }
        }
      }
    }
  }

  public function isCurrentUserAllowed($controller, $action) : bool {
    if ($this->authentificationService->hasIdentity()) {
      $identity = $this->authentificationService->getIdentity();
      $isAllowed = false;
      foreach ($identity->rolesIds as $roleId) {
        if ($this->acl->isAllowed($roleId, $controller, $action)) {
          $isAllowed = true;
        }
      }
      return $isAllowed;
    } else {
      return $this->acl->isAllowed(\System\Service\RoleManager::ID_ROLE_GUEST, $controller, $action);
    }
  }

  public function canCurrentUserViewObject(\System\iObjectWithAuthorization $object) : bool {
    if ($this->authentificationService->hasIdentity()) {
      return $object->canBeViewedByUser($this->authentificationService->getIdentity());
    } else {
      return $object->canBeViewedByUser(null);
    }
  }

  public function canCurrentUserChangeObject(\System\iObjectWithAuthorization $object) : bool {
    if ($this->authentificationService->hasIdentity()) {
      return $object->canBeChangedByUser($this->authentificationService->getIdentity());
    } else {
      return $object->canBeChangedByUser(null);
    }
  }

  protected function existsControllerAction(\Zend\Mvc\MvcEvent $e) : bool {
    $return = false;
    if (array_key_exists($e->getRouteMatch()->getParam('controller'), $this->config['controllers']['invokables'])) {
      $classMethods = get_class_methods($this->config['controllers']['invokables'][$e->getRouteMatch()->getParam('controller')]);
      if (in_array($e->getRouteMatch()->getParam('action') . 'Action', $classMethods)) {
        $return = true;
      }
    }
    if (array_key_exists($e->getRouteMatch()->getParam('controller'), $this->config['controllers']['factories'])) {
      $classMethods = get_class_methods($e->getRouteMatch()->getParam('controller'));
      if (in_array($e->getRouteMatch()->getParam('action') . 'Action', $classMethods)) {
        $return = true;
      }
    }
    return $return;
  }

  public function isControlerUnderAuthorizationControl(string $controllerName): bool {
    $controllerNamespaceParts = \explode('\\', $controllerName);
    if (isset($controllerNamespaceParts[0]) && \in_array($controllerNamespaceParts[0], self::MODULES)) {
      return true;
    }
    return false;
  }

}