<?php

namespace System;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;

class Module {
  
  const VERSION = '3.0.0';

  public function getConfig() {
    return include __DIR__ . '/../config/module.config.php';
  }

  public function getServiceConfig() {
    return array(
      'factories' => array(
        'AuthentificationService' => function($sm) {
          $authAdapter = $sm->get(\System\Service\AuthAdapter::class);
          $authService = new \Zend\Authentication\AuthenticationService();
          $authService->setAdapter($authAdapter);
          $authService->setStorage(new \Zend\Authentication\Storage\Session());
          return $authService;
        },
        \System\Service\ErrorHandling::class => function($sm) {
          $filename = 'exceptions_' . date('Y') . '_' . date('m') . '.log';
          $logger = new Logger();
          $writer = new LogWriterStream('./data/logs/' . $filename);
          $logger->addWriter($writer);
          $service = new \System\Service\ErrorHandling($logger);
          return $service;
        },
        \System\Acl::class => function($sm) {
          $cache = $sm->get('CacheAcl');
          if (!$cache->hasItem('acl')) {
            $acl = new \System\Acl();
            $entityManager = $sm->get('doctrine.entitymanager.orm_default');
            $acl->setRoles($entityManager->getRepository(\System\Entity\Role::class)->findAll());
            $acl->setResources($sm->get('Config'));
            $acl->setRights($entityManager->getRepository(\System\Entity\Right::class)->findAll());
            $cache->addItem('acl', serialize($acl));
          } else {
            $acl = unserialize($cache->getItem('acl'));
          }
          return $acl;
        },
        \System\Service\Authorization::class => function($sm) {
          $acl = $sm->get(\System\Acl::class);
          $authenticationService = $sm->get('AuthentificationService');
          $service = new \System\Service\Authorization($acl, $authenticationService);
          return $service;
        },
        \System\Form\RightsForm::class => \System\Form\Factory\RightsFormFactory::class,
        \System\Service\UserManager::class => function ($sm) {
          $entityManager = $sm->get('doctrine.entitymanager.orm_default');
          return new \System\Service\UserManager($entityManager);
        },
        \System\Service\RoleManager::class => function ($sm) {
          $entityManager = $sm->get('doctrine.entitymanager.orm_default');
          return new \System\Service\RoleManager($entityManager);
        },
        \System\Service\AuthAdapter::class => function ($sm) {
          $entityManager = $sm->get('doctrine.entitymanager.orm_default');
          return new \System\Service\AuthAdapter($entityManager);
        }
      ),
    );
  }

  public function getViewHelperConfig() {
    return array(
      'factories' => array(
        'authArea' => function($container) {
          return new View\Helper\AuthArea($container->get('AuthentificationService'));
        },
        'isAllowed' => function($container) {
          $config = $container->get('Config');
          $authorizationService = $container->get(\System\Service\Authorization::class);
          return new View\Helper\IsAllowed($authorizationService, $config['router']['routes']);
        },
        'absoluteUrl' => function($container) {
          return new View\Helper\AbsoluteUrl($container->get('Request'));
        },
        'flashMessages' => function($sm) {
          $flashMessenger = new \Zend\Mvc\Plugin\FlashMessenger\FlashMessenger();
          return new \System\View\Helper\FlashMessages($flashMessenger);
        },
        'canView' => function($container) {
          $authorizationService = $container->get(\System\Service\Authorization::class);
          return new View\Helper\CanView($authorizationService);
        },
        'canChange' => function($container) {
          $authorizationService = $container->get(\System\Service\Authorization::class);
          return new View\Helper\CanChange($authorizationService);
        },
      ),
    );
  }

  public function onBootstrap(\Zend\Mvc\MvcEvent $e) {
    $application = $e->getApplication();
    $sm = $application->getServiceManager();
    $eventManager = $application->getEventManager();
    $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH,
            function($e) use ($sm) {
      $sm->get(\System\Service\Authorization::class)
              ->doAuthorization($e);
    }, 100);

    $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR,
            function($event) {
      $exception = $event->getResult()->exception;
      if ($exception) {
        $sm = $event->getApplication()->getServiceManager();
        $service = $sm->get(\System\Service\ErrorHandling::class);
        $service->logException($exception);
      }
    });
  }

}