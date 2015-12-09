<?php
class DataObjectAPIExtension extends Extension {

    private static $data_transforms = array(
        'int' => '/^(Int|ForeignKey)$/',
        'float' => '/^((Float|Double|Decimal|Currency|Percentage)(\(.+\))?)$/',
        'string' => '/^((Varchar)(\(.+\))?)$/',
        'date' => '/^((Date|SS_Datetime)(\(.+\))?)$/',
        'bool' => '/^(Boolean)$/'
    );

    public function forAPI () {
        return $this->owner->getAllFieldsExcludingAPI();
    }

    public function getAllFieldsExcludingAPI ($toExclude = array()) {
        $obj = $this->owner;

        // All Field Names
        $fieldNames = array_merge(
            array_keys($obj->allDatabaseFields()),
            array_keys($obj->hasOne()),
            // array_keys($obj->belongsTo()),
            array_keys($obj->hasMany())
            // array_keys($obj->manyMany())
        );

        // TODO: use $toExclude

        return $obj->getFieldsForAPI($fieldNames);
    }

    public function getFieldsForAPI ($fieldNames) {
        $obj = $this->owner;

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
                $fields[$fieldName] = $obj->getObjectForAPI($fieldName, $singular[$fieldName]);
            }

            // Is collection of objects?
            elseif (array_key_exists($fieldName, $collection)) {
                $fields[$fieldName] = $obj->getObjectCollectionForAPI($fieldName, $db[$fieldName]);
            }

            // TODO: Is a custom getter?

            else {
                error_log('Field not found');
            }
        }

        return $fields;
    }

    public function getDBFieldForAPI ($fieldName, $dataType) {
        $obj = $this->owner;
        $type = 'raw';
        $value = null;
        // Loop tests
        foreach (self::$data_transforms as $testType => $regex) {
            if (preg_match($regex, $dataType)) {
                $type = $testType;
                break;
            }
        }

        error_log($type);

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

    public function getObjectForAPI ($fieldName, $dataType) {
        $fieldObj = $this->owner->$fieldName();

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
            return $this->owner->$fieldName()->forApi();
        }

    }

    public function getObjectCollectionForAPI ($fieldNames, $dataType) {
        $data = array();
        foreach ($fieldNames as $fieldName => $dataType) {
            $data[] = $this->owner->getObjectForAPI($fieldName, $dataType);
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
