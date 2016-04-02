<?php

namespace System\Controller;

interface FactoryInterface {

  public static function getCreatedClassName(): string;
}
