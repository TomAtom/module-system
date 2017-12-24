<?php

namespace System\Service;

class ErrorHandling {

  protected $logger;

  function __construct(\Zend\Log\LoggerInterface $logger) {
    $this->logger = $logger;
  }

  function logException(\Throwable $e) : void {
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