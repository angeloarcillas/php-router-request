<?php

namespace SimpleRouter\Traits;

/**
 * 
 */
trait RouterTrait
{
  /**
   * Redirect back to previous url
   */
  public function back(): void
  {
    // check if previous uri exist
    if (isset($_SERVER["HTTP_REFERER"])) {
      // redirect to previous url
      header("location: {$_SERVER["HTTP_REFERER"]}", true, 302);
      exit;
    }
  }

  public function setHost(string $newHost)
  {
    static::$host = $newHost;
  }

  public function setControllerNamespace(string $newControllerNamespace)
  {
    static::$controllerNamespace = $newControllerNamespace;
  }
}
