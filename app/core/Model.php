<?php
/**
 * Model.php — Base class for all data models.
 *
 * Provides the PDO connection. Child models use prepared statements only —
 * never concatenate user input into SQL strings.
 */

declare(strict_types=1);

abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = db();
    }
}
