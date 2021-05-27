<?php

/**
 * Redirect to new Page
 *
 * @param null|string $path
 * @param int $status
 * @param array $headers
 */
if (!function_exists('redirect')) {
  function redirect(
    ?string $path = null, // path to redirect
    int $status = 302, // http status code
    array $headers = [] // additional request headers
  ) {
    // check if no $path
    if (!$path) {
      // return Router class
      return new \App\Http\Router();
    }

    // check if headers already sent
    if (headers_sent() === false) {
      // loop headers
      foreach ($headers as $header) header($header);

      // convert user.settings to user/settings
      $realSubPath = str_replace('.', '/', e($path));
      // trim excess forward slash
      $realPath = '/' . trim($realSubPath, '/');
      // redirect
      header("location:{$realPath}", true, $status);
      exit;
    }

    return false;
  }
}

/**
 * Get all request
 *
 * @param null|string $key
 *
 * @return array|string
 */
if (!function_exists("request")) {
  function request(?string $key = null)
  {
    // create Request instance
    $request = new App\Http\Request();

    // return request or request class
    return $key ?  $request->$key : $request;
  }
}

/**
 * Encode string
 *
 * @param string $string
 */
if (!function_exists("e")) {
  function e(string $string)
  {
    return htmlspecialchars($string, ENT_QUOTES);
  }
}