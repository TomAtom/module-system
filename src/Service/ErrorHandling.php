<?php

namespace System\Service;

class ErrorHandling {

  protected $logger;

  function __construct($logger) {
    $this->logger = $logger;
  }

  function logException(\Throwable $e) {
    $trace = $e->getTraceAsString();
    $i = 1;
    do {
      $messages[] = $i++ . ": " . $e->getMessage();
    } while ($e = $e->getPrevious());

    $log = "Exception:\n" . implode("\n", $messages);
    $log .= "\nTrace:\n" . $trace;

    $this->logger->err($log);
  }

}