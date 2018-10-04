<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use PDO;

/**
 * PdoConnectionDouble
 * 
 * @author   czukowski
 * @license  MIT License
 */
class PdoConnectionDouble extends PDO
{
    /**
     * @var  array
     */
    private $attributes = [];

    /**
     * @param  string  $driverName
     */
    public function __construct($driverName)
    {
        // No calling parent constructor!
        $this->attributes[PDO::ATTR_DRIVER_NAME] = $driverName;
        $this->attributes[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;
    }

    /**
     * @param   integer  $attribute
     * @return  mixed
     */
    public function getAttribute($attribute)
    {
        return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : NULL;
    }

    /**
     * @param   string   $value
     * @param   integer  $type
     * @return  string
     */
    public function quote($value, $type = NULL)
    {
        return $type === PDO::PARAM_LOB
            ? MySqlEscapingHelper::escapeBinary($value)
            : MySqlEscapingHelper::escapeText($value);
    }
}
