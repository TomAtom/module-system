<?php

namespace System\View\Helper;

class FlashMessages extends \Zend\View\Helper\AbstractHelper {

  protected $flashMessenger;

  public function __construct(\Zend\Mvc\Plugin\FlashMessenger\FlashMessenger $messenger) {
    $this->flashMessenger = $messenger;
  }

  public function __invoke() {
    $output = null;
    foreach ($this->flashMessenger->getMessages() as $message) {
      $output .= '<div class="alert">' . $message . '</div>';
    }
    foreach ($this->flashMessenger->getSuccessMessages() as $message) {
      $output .= '<div class="alert alert-success">' . $message . '</div>';
    }
    foreach ($this->flashMessenger->getErrorMessages() as $message) {
      $output .= '<div class="alert alert-danger">' . $message . '</div>';
    }
    foreach ($this->flashMessenger->getWarningMessages() as $message) {
      $output .= '<div class="alert alert-warning">' . $message . '</div>';
    }
    foreach ($this->flashMessenger->getInfoMessages() as $message) {
      $output .= '<div class="alert alert-info">' . $message . '</div>';
    }
    return $output;
  }

}