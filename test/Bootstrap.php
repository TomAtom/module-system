<?php

namespace SystemTest;

error_reporting(E_ALL);
chdir(\dirname(\dirname(\dirname(__DIR__))));

class Bootstrap {

  public static function init() {
    include __DIR__ . '/../../../vendor/autoload.php';
  }

}
Bootstrap::init();
