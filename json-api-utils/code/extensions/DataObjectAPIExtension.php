<?php
/**
 * Usage:
 *
 * $dataObject->getAllFieldsExcludingAPI(array('ID', 'ClassName'));
 * // ['Title'=>'Some Object', ... 'Created'=>'2015-11-04T00:32:31+11:00']
 *
 *
 * $dataObject->getFieldsForAPI(array('ID', 'ParentID' 'MenuTitle'));
 * // ['ID'=>3, 'ParentID'=>0, 'MenuTitle'=>'Out Company']
 *
 * Recommented Usage:
 * Define forAPI() on each subclass of DataObject.
 * In custom forAPI methods make calls to getFieldsForAPI explicitly defining
 * desired fields
 *
 */

class DataObjectAPIExtension extends Extension {

    private static $db_field_transforms = array(
        'int' => '/^(Int|ForeignKey)$/',
        'float' => '/^((Float|Double|Decimal|Currency|Percentage)(\(.+\))?)$/',
        'string' => '/^((Varchar)(\(.+\))?)$/',
        'date' => '/^((Date|SS_Datetime)(\(.+\))?)$/',
        'bool' => '/^(Boolean)$/'
    );

    // TODO: move this to yml config
    private static $always_exclude = array(
        'HasBrokenFile',
        'HasBrokenLink',
        'ReportClass',
        'CanViewType',
        'CanEditType'
    );

    public function forAPI () {
        return $this->owner->getAllFieldsExcludingAPI();
    }

    /**
     * Get all fields excluding key names given in $toExclude
     */
    public function getAllFieldsExcludingAPI ($toExclude = array()) {
        $obj = $this->owner;

        // All Field Names
        $fieldNames = array_merge(
            array_keys($obj->allDatabaseFields()),
            array_keys($obj->hasOne()),
            array_keys($obj->hasMany())
        );

        // Removed fields in $toExclude
        $fieldNames = array_diff($fieldNames, $toExclude);

        return $obj->getFieldsForAPI($fieldNames);
    }

    /**
     * Get only fields in $fieldNames
     */
    public function getFieldsForAPI ($fieldNames) {
        $obj = $this->owner;

        // Remove sensitive fields
        $fieldNames = array_diff($fieldNames, self::$always_exclude);

        $fields = array();

        $db = $obj->allDatabaseFields();
        $singular = array_merge(
            $obj->hasOne(),
            $obj->belongsTo()
        );
        $collection = array_merge(
            $obj->hasMany(),
            $obj->manyMany()
        );

        foreach ($fieldNames as $fieldName) {

            // Is db field?
            if (array_key_exists($fieldName, $db)) {
                $fields[$fieldName] = $obj->getDBFieldForAPI($fieldName, $db[$fieldName]);
            }

            // Is single Object?
            elseif (array_key_exists($fieldName, $singular)) {
                $fields[$fieldName] = $obj->getObjectForAPI($obj->$fieldName(), $singular[$fieldName]);
            }

            // Is collection of objects?
            elseif (array_key_exists($fieldName, $collection)) {
                $fields[$fieldName] = $obj->getObjectCollectionForAPI($fieldName, $collection[$fieldName]);
            }

            // TODO: Is a custom getters?

            else {
                error_log('Field not found');
            }
        }

        return $fields;
    }

    /**
     * Parse a DB field for API output
     */
    public function getDBFieldForAPI ($fieldName, $dataType) {
        $obj = $this->owner;
        $type = 'raw';
        $value = null;
        // Loop tests
        foreach (self::$db_field_transforms as $testType => $regex) {
            if (preg_match($regex, $dataType)) {
                $type = $testType;
                break;
            }
        }

        // Transform value
        switch ($type) {
            case 'int':
                $value = intval($obj->$fieldName);
                break;
            case 'float':
                $value = floatval($obj->$fieldName);
                break;
            case 'date':
                $value = $obj->obj($fieldName)->Rfc3339();
                break;
            case 'bool':
                $value = boolval($obj->$fieldName);
                break;
            default:    // raw, string or other
                $value = $obj->$fieldName;
                break;
        }

        return $value;
    }

    public function getObjectForAPI ($fieldObj, $dataType) {

        if ($fieldObj && !$fieldObj->ID) {
            return null;
        }

        elseif (is_a($fieldObj, 'Image')) {
            return array(
                'type' => $fieldObj->getFileType(),
                'size' => $fieldObj->getSize(),
                'url' => $fieldObj->AbsoluteLink(),
                'width' => $fieldObj->getWidth(),
                'height' => $fieldObj->getHeight()
            );
        }

        elseif (is_a($fieldObj, 'File')) {
            return array(
                'name' => $fieldObj->getFileType(),
                'size' => $fieldObj->getSize(),
                'type' => $fieldObj->getFileType(),
                'url' => $fieldObj->AbsoluteLink()
            );
        }

        elseif (is_a($fieldObj, 'SiteTree')) {
            return array(
                'ID' => $fieldObj->ID,
                'ClassName' => $fieldObj->ClassName,
                'Title' => $fieldObj->Title,
                'MenuTitle' => $fieldObj->MenuTitle
            );
        }

        else {
            return $fieldObj->forApi();
        }

    }

    public function getObjectCollectionForAPI ($fieldName, $dataType) {
        $data = array();
        $fieldObjs = $this->owner->$fieldName();
        foreach ($fieldObjs as $fieldObj) {
            $data[] = $this->owner->getObjectForAPI($fieldObj, $dataType);
        }
        return $data;
    }

    public function allDatabaseFields () {
        $obj = $this->owner;
        return array_merge(
            array(
                'ID' => 'Int',
                'ClassName' => 'Varchar',
                'LastEdited' => 'SS_Datetime',
                'Created' => 'SS_Datetime',
                'Title' => 'Text'
            ),
            $obj->inheritedDatabaseFields()
        );
    }
}
