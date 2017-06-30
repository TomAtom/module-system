<?php
return array(
  'controllers' => array(
    'factories' => [
      'System\Controller\Role' => 'System\Controller\Factory\RoleController',
      'System\Controller\Authentification' => 'System\Controller\Factory\AuthentificationController',
      'System\Controller\User' => 'System\Controller\Factory\UserController',
    ]
  ),
  'router' => array(
    'routes' => array(
      'user' => array(
        'type' => 'segment',
        'options' => array(
          'route' => '/user[/action/:action][/id/:id][/page/:page]',
          'constraints' => array(
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
            'page' => '[0-9]+',
          ),
          'defaults' => array(
            'controller' => 'System\Controller\User',
            'action' => 'index',
          ),
        ),
      ),
      'authentification' => array(
        'type' => 'segment',
        'options' => array(
          'route' => '/authentification[/:action]',
          'constraints' => array(
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          ),
          'defaults' => array(
            'controller' => 'System\Controller\Authentification',
            'action' => 'login',
          ),
        ),
      ),
      'role' => array(
        'type' => 'segment',
        'options' => array(
          'route' => '/role[/:action][/:id]',
          'constraints' => array(
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => array(
            'controller' => 'System\Controller\Role',
            'action' => 'index',
          ),
        ),
      ),
    ),
  ),
  'view_manager' => array(
    'template_path_stack' => array(
      'system' => __DIR__ . '/../view',
    ),
    'template_map' => array(
      'layout/system/unauthorizedAccess' => __DIR__ . '/../view/layout/unauthorizedAccess.phtml',
    ),
  ),
  'service_manager' => array(
    'abstract_factories' => array(
      'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
    ),
  ),
  'caches' => array(
    'CacheAcl' => array(
      'adapter' => array(
        'name' => 'filesystem'
      ),
      'options' => array(
        'cache_dir' => "./data/cache/aclcache",
      ),
    ),
  ),
);
