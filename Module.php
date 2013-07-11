<?php

namespace System;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;

use System\Model\UserTable;
use System\Model\User;
use System\Model\RoleTable;
use System\Model\RightTable;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                // db tables
                'System\Model\UserTable' =>  function($sm) {
                    $gateway = $sm->get('UserTableGateway');
                    $table     = new UserTable($gateway);
                    return $table;
                },
                'UserTableGateway' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype($sm->get('System\Model\User'));
                    return new TableGateway('system_users', $dbAdapter, null, $resultSetPrototype);
                },
                'System\Model\User' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new User('id_user', 'system_users', $dbAdapter);
                },
                'System\Model\RoleTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new RoleTable($dbAdapter);
                    return $table;
                },
                'System\Model\RightTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new RightTable($dbAdapter);
                    return $table;
                },
                // forms
                'System\Form\UserForm' =>  function($sm) {
                    $roleTable = $sm->get('System\Model\RoleTable');
                    $form     = new \System\Form\UserForm();
                    $form->setRoles($roleTable);
                    return $form;
                },
                'AuthService' => function($sm) {
	            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	            $dbTableAuthAdapter  = new \Zend\Authentication\Adapter\DbTable($dbAdapter, 'system_users', 'email', 'password', 'MD5(?)');
	            $authService = new \Zend\Authentication\AuthenticationService();
	            $authService->setAdapter($dbTableAuthAdapter);
	            $authService->setStorage(new \Zend\Authentication\Storage\Session());
	            return $authService;
	        }, 
                'System\Service\ErrorHandling' =>  function($sm) {
                    $filename = 'exceptions_' . date('F') . '.log';
                    $logger = new Logger();
                    $writer = new LogWriterStream('./data/logs/' . $filename);
                    $logger->addWriter($writer);
                    $service = new Service\ErrorHandling($logger);
                    return $service;
                },                       
            ),
        );
    }
    
    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'authArea' => function($sm) {
                    $locator = $sm->getServiceLocator();
                    return new View\Helper\AuthArea($locator->get('AuthService'));
                },
                'isAllowed' => function($sm) {
                    $locator = $sm->getServiceLocator();
                    $config = $locator->get('Config');
                    return new View\Helper\IsAllowed(\System\Acl::getInstance(), $config['router']['routes']);
                },
                'absoluteUrl' => function($sm) {
                    $locator = $sm->getServiceLocator(); // $sm is the view helper manager, so we need to fetch the main service manager
                    return new View\Helper\AbsoluteUrl($locator->get('Request'));
                },
                'flashMessages' => function($sm) {
                    return new View\Helper\FlashMessages(new \Zend\Mvc\Controller\Plugin\FlashMessenger);
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
                                    $sm->get('Authorization')
                                               ->doAuthorization($e);
                                },
                               100);
                                
        $eventManager->attach('dispatch.error', function($event){
            $exception = $event->getResult()->exception;
            if ($exception) {
                $sm = $event->getApplication()->getServiceManager();
                $service = $sm->get('System\Service\ErrorHandling');
                $service->logException($exception);
            }
        });
    }
    
    public function onDispatchError(\Zend\Mvc\MvcEvent $e) {
        $vm = $e->getViewModel();
        $vm->setTemplate('layout/layoutDetail');
    }

}