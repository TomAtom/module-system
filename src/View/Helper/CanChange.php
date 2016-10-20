<?php

namespace System\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CanChange extends AbstractHelper {

  protected $authorizationService;

  public function __construct(\System\Service\Authorization $authorizationService) {
    $this->authorizationService = $authorizationService;
  }

  public function __invoke(\System\iObjectWithAuthorization $object) {
    return $this->authorizationService->canCurrentUserChangeObject($object);
  }

}
