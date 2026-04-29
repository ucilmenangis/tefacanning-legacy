<?php
/**
 * Auth bootstrap — loads OOP classes and starts session.
 *
 * All procedural functions removed. Use instead:
 *   Auth::admin()->requireAuth()      (was requireAdmin())
 *   Auth::customer()->requireAuth()   (was requireCustomer())
 *   CsrfService::field()              (was csrfField())
 *   CsrfService::verify()             (was verifyCsrf())
 *   FlashMessage::set()               (was setFlash())
 *   FlashMessage::render()            (was renderFlash())
 */

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/exceptions/AppException.php';
require_once __DIR__ . '/../classes/exceptions/DatabaseException.php';
require_once __DIR__ . '/../classes/exceptions/AuthException.php';
require_once __DIR__ . '/../classes/exceptions/CsrfException.php';
require_once __DIR__ . '/../classes/BaseService.php';
require_once __DIR__ . '/../classes/CsrfService.php';
require_once __DIR__ . '/../classes/FlashMessage.php';
require_once __DIR__ . '/../classes/SessionGuard.php';
require_once __DIR__ . '/../classes/AdminGuard.php';
require_once __DIR__ . '/../classes/CustomerGuard.php';
require_once __DIR__ . '/../classes/Auth.php';

Auth::startSession();
