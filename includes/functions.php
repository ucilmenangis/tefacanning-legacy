<?php
/**
 * Functions bootstrap — loads Database class.
 *
 * All procedural db_* functions removed. Use instead:
 *   Database::getInstance()->fetch()     (was db_fetch())
 *   Database::getInstance()->fetchAll()  (was db_fetch_all())
 *   Database::getInstance()->insert()    (was db_insert())
 *   Database::getInstance()->update()    (was db_update())
 *   Database::getInstance()->delete()    (was db_delete())
 *
 * In service classes that extend BaseService, use:
 *   $this->fetch() / $this->fetchAll() / etc.
 */

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/exceptions/AppException.php';
require_once __DIR__ . '/../classes/exceptions/DatabaseException.php';
require_once __DIR__ . '/../classes/BaseService.php';
