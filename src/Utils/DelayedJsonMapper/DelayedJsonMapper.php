<?php
/*
 * Copyright 2022 The LazyJsonMapper Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Bottel\Utils\DelayedJsonMapper;

use Bottel\Utils\DelayedJsonMapper\Utilities;
use Bottel\Utils\DelayedJsonMapper\Exception\LazyJsonMapperException;
use Bottel\Utils\DelayedJsonMapper\Exception\LazySerializationException;
use Bottel\Utils\DelayedJsonMapper\Exception\LazyUserException;
use Bottel\Utils\DelayedJsonMapper\Exception\MagicTranslationException;
use Bottel\Utils\DelayedJsonMapper\Export\ClassAnalysis;
use Bottel\Utils\DelayedJsonMapper\Export\PropertyDescription;
use Bottel\Utils\DelayedJsonMapper\Magic\FunctionTranslation;
use Bottel\Utils\DelayedJsonMapper\Property\PropertyMapCache;
use Bottel\Utils\DelayedJsonMapper\Property\PropertyMapCompiler;
use Bottel\Utils\DelayedJsonMapper\Property\UndefinedProperty;
use Bottel\Utils\DelayedJsonMapper\Property\ValueConverter;
use \Bottel\Utils\DelayedResponse;
use stdClass;
use ArrayAccess;

class DelayedJsonMapper implements ArrayAccess {
    /**
     * @var bool
     */
    const ALLOW_VIRTUAL_PROPERTIES = true;

    /**
     * @var bool
     */
    const ALLOW_VIRTUAL_FUNCTIONS = true;

    /**
     * @var bool
     */
    const USE_MAGIC_LOOKUP_CACHE = true;

    /**
     * @var array
     */
    const JSON_PROPERTY_MAP = [];

    /**
     * @var array
     */
    private static $_magicLookupCache = [];

    /**
     * @var PropertyMapCache
     */
    private static $_propertyMapCache;

    /**
     * @var array
     */
    private $_compiledPropertyMapLink;

    /**
     * @var array|DealyedResponse
     */
    private $_objectData;
    
    /**
     * __construct
     *
     * @param  array|DelayedResponse $datas
     * @param  bool $requireAnalysis
     * @return void
     */
    final public function __construct(array|DelayedResponse $objectData = [], bool $requireAnalysis = false){
        if(self::$_propertyMapCache === null) {
            self::$_propertyMapCache = new PropertyMapCache();
        }
        $thisClassName = get_class($this);
        if(!isset(self::$_propertyMapCache->classMaps[$thisClassName])) {
            PropertyMapCompiler::compileClassPropertyMap(
                self::$_propertyMapCache,
                $thisClassName
            );
        }
        $this->_compiledPropertyMapLink = &self::$_propertyMapCache->classMaps[$thisClassName];
        $this->assignObjectData($objectData, $requireAnalysis);
    }
    
    /**
     * assignObjectData
     *
     * @param  array|DelayedResponse $objectData
     * @param  bool $requireAnalysis
     * @return void
     */
    final public function assignObjectData(array|DelayedResponse $objectData = [], bool $requireAnalysis = false): void {
        $this->_objectData = $objectData;
        if($requireAnalysis) {
            $analysis = $this->exportClassAnalysis();
            if($analysis->hasProblems()) {
                throw new LazyJsonMapperException(
                    $analysis->generateNiceSummariesAsString()
                );
            }
        }

        try {
            $this->_init();
        }catch(LazyUserException $e) {
            throw $e;
        }catch(\Exception $e) {
            throw new LazyUserException(
                'Invalid exception thrown by _init(). Must use LazyUserException.'
            );
        }
    }
    
    /**
     * exportPropertyDescriptions
     *
     * @param  bool $allowRelativeTypes
     * @param  bool $includeUndefined
     * @return PropertyDescription[]
     */
    final public function exportPropertyDescriptions(bool $allowRelativeTypes = false, bool $includeUndefined = false): array {
        if(!is_bool($allowRelativeTypes) || !is_bool($includeUndefined)) {
            throw new LazyJsonMapperException('The function arguments must be booleans.');
        }

        $descriptions = [];
        $ownerClassName = get_class($this);
        foreach($this->_compiledPropertyMapLink as $propName => $propDef) {
            $descriptions[$propName] = new PropertyDescription(
                $ownerClassName,
                $propName,
                $propDef,
                $allowRelativeTypes
            );
        }

        if($includeUndefined) {
            $undefinedProperty = UndefinedProperty::getInstance();
            if($this->_objectData instanceof DelayedResponse){
                $this->_objectData = $this->_objectData->fetch();
            }
            foreach($this->_objectData as $propName => $v) {
                if(!isset($descriptions[$propName])) {
                    $descriptions[$propName] = new PropertyDescription(
                        $ownerClassName,
                        $propName,
                        $undefinedProperty,
                        $allowRelativeTypes
                    );
                }
            }
        }
        ksort($descriptions, SORT_NATURAL | SORT_FLAG_CASE);
        return $descriptions;
    }
    
    /**
     * printPropertyDescriptions
     *
     * @param  bool $showFunctions
     * @param  bool $allowRelativeTypes
     * @param  bool $includeUndefined
     * @return void
     */
    final public function printPropertyDescriptions(bool $showFunctions = true, bool $allowRelativeTypes = false, bool $includeUndefined = false): void {
        if (!is_bool($showFunctions) || !is_bool($allowRelativeTypes) || !is_bool($includeUndefined)) {
            throw new LazyJsonMapperException('The function arguments must be booleans.');
        }
        $descriptions = $this->exportPropertyDescriptions(
            $allowRelativeTypes,
            $includeUndefined
        );
        $equals_bar = str_repeat('=', 60);
        $dash_bar = str_repeat('-', 60);

        printf(
            '%s%s> Class:    "%s"%s  Supports: [%s] Virtual Functions [%s] Virtual Properties%s%s%s  Show Functions: %s.%s  Allow Relative Types: %s.%s  Include Undefined Properties: %s.%s%s%s',
            $equals_bar,
            PHP_EOL,
            Utilities::createStrictClassPath(get_class($this)),
            PHP_EOL,
            static::ALLOW_VIRTUAL_FUNCTIONS ? 'X' : ' ',
            static::ALLOW_VIRTUAL_PROPERTIES ? 'X' : ' ',
            PHP_EOL,
            $dash_bar,
            PHP_EOL,
            $showFunctions ? 'Yes' : 'No',
            PHP_EOL,
            $allowRelativeTypes ? 'Yes' : 'No',
            PHP_EOL,
            $includeUndefined ? 'Yes' : 'No',
            PHP_EOL,
            $equals_bar,
            PHP_EOL
        );
    
        $lastPropertyNum = count($descriptions);
        $padNumDigitsTo = strlen($lastPropertyNum);
        if($padNumDigitsTo < 2) {
            $padNumDigitsTo = 2;
        }
        $alignPadding = 4 + (2 * $padNumDigitsTo);
        $thisPropertyNum = 0;
        foreach($descriptions as $property) {
            $thisPropertyNum++;
            printf(
                '  #%s/%s: "%s"%s%s%s: "%s"%s%s',
                str_pad($thisPropertyNum, $padNumDigitsTo, '0', STR_PAD_LEFT),
                str_pad($lastPropertyNum, $padNumDigitsTo, '0', STR_PAD_LEFT),
                $property->name,
                !$property->is_defined ? ' (Not in class property map!)' : '',
                PHP_EOL,
                str_pad('* Type', $alignPadding, ' ', STR_PAD_LEFT),
                $property->type,
                $property->is_basic_type ? ' (Basic PHP type)' : '',
                PHP_EOL
            );

            if ($showFunctions) {
                foreach (['has', 'is', 'get', 'set', 'unset'] as $function) {
                    printf(
                        '%s: %s%s',
                        str_pad($function, $alignPadding, ' ', STR_PAD_LEFT),
                        $property->{"function_{$function}"},
                        PHP_EOL
                    );
                }
            }
            if ($thisPropertyNum !== $lastPropertyNum) {
                echo $dash_bar.PHP_EOL;
            }
        }
        if (empty($descriptions)) {
            echo '- No properties.'.PHP_EOL;
        }
        echo $equals_bar.PHP_EOL;
    }
    
    /**
     * exportObjectDataCopy
     *
     * @param  string $objectRepresentation
     * @return stdClass|array
     */
    final public function exportObjectDataCopy(string $objectRepresentation = 'array'): stdClass|array {
        if (!in_array($objectRepresentation, ['stdClass', 'array'], true)) {
            throw new LazyJsonMapperException(sprintf(
                'Invalid object representation type "%s". Must be either "stdClass" or "array".',
                $objectRepresentation
            ));
        }
        $copy = clone $this;
        $analysis = $copy->exportClassAnalysis(false); // Never throws.
        if (!empty($analysis->bad_definitions)) { // Ignore missing_definitions
            $problemSummaries = $analysis->generateNiceSummaries();

            throw new LazyJsonMapperException(sprintf(
                'Unable to convert data to %s: %s',
                $objectRepresentation,
                $problemSummaries['bad_definitions']
            ));
        }

        array_walk_recursive($copy->_objectData, function (&$value, $key) use ($objectRepresentation) {
            if(is_object($value)) {
                if(!$value instanceof self) {
                    throw new LazyJsonMapperException(sprintf(
                        'Unable to convert data to %s: Unexpected "%s" object in property/key "%s", but we expected an instance of a LazyJsonMapper object.',
                        $objectRepresentation,
                        Utilities::createStrictClassPath(get_class($value)),
                        $key
                    ));
                }
                $value = $value->exportObjectDataCopy($objectRepresentation);
            }
        });

        if($objectRepresentation === 'stdClass') {
            $outputContainer = new stdClass();
            foreach($copy->_objectData as $k => $v) {
                $outputContainer->{(string) $k} = $v;
            }
        }else{
            $outputContainer = $copy->_objectData;
        }
        return $outputContainer;
    }
    
    /**
     * _init
     */
    protected function _init(){
    }

    /**
     * asArray
     *
     * @return array
     */
    final public function asArray(): array {
        return $this->exportObjectDataCopy('array');
    }
    
    /**
     * asJson
     *
     * @param  ?int $options
     * @param  ?int $depth
     * @return string
     */
    final public function asJson(int $options = 0, int $depth = 512): string {
        if(!is_int($options) || !is_int($depth)) {
            throw new LazyJsonMapperException('Invalid non-integer function argument.');
        }
        $objectData = $this->exportObjectDataCopy('stdClass');
        $jsonString = @json_encode($objectData, $options, $depth);
        if($jsonString === false) {
            throw new LazyJsonMapperException(sprintf(
                'Failed to encode JSON string (error %d: "%s").',
                json_last_error(), json_last_error_msg()
            ));
        }
        return $jsonString;
    }
    
    /**
     * asStdClass
     *
     * @return stdClass
     */
    final public function asStdClass(): stdClass {
        return $this->exportObjectDataCopy('stdClass');
    }
    
    /**
     * printJson
     *
     * @param  ?bool $prettyPrint
     * @param  ?int $depth
     * @return void
     */
    final public function printJson(bool $prettyPrint = true, int $depth = 512): void {
        $options = ($prettyPrint ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = $this->asJson($options, $depth);
        if($prettyPrint && PHP_EOL !== "\n") {
            $json = str_replace("\n", PHP_EOL, $json);
        }
        echo $json.PHP_EOL;
    }
    
    /**
     * jsonSerialize
     *
     * @return stdClass
     */
    final public function jsonSerialize(): stdClass {
        return $this->exportObjectDataCopy('stdClass');
    }
    
    /**
     * exportClassAnalysis
     *
     * @param  bool $recursiveScan
     * @return ClassAnalysis
     */
    final public function exportClassAnalysis(bool $recursiveScan = true): ClassAnalysis {
        $result = new ClassAnalysis();
        $definitionSource = get_class($this);
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        foreach($this->_objectData as $propName => $value) {
            $propDef = $this->_getPropertyDefinition($propName);
            try {
                $value = $this->_getProperty($propName);
                if($recursiveScan && $value !== null && $propDef->isObjectType) {
                    if($propDef->arrayDepth > 0) {
                        array_walk_recursive($value, function(&$obj)use(&$result){
                            if(is_object($obj)) {
                                $result->mergeAnalysis($obj->exportClassAnalysis());
                            }
                        });
                    }else{
                        $result->mergeAnalysis($value->exportClassAnalysis());
                    }
                }
            }catch(LazyJsonMapperException $e) {
                $result->addProblem(
                    $definitionSource,
                    'bad_definitions',
                    $e->getMessage()
                );
            }
            if ($propDef instanceof UndefinedProperty) {
                $result->addProblem(
                    $definitionSource,
                    'missing_definitions',
                    (string) $propName
                );
            }
        }

        $result->sortProblemLists();
        return $result;
    }
    
    /**
     * _hasPropertyDefinition
     *
     * @param  mixed $propName
     * @return bool
     */
    final protected function _hasPropertyDefinition($propName): bool {
        return isset($this->_compiledPropertyMapLink[$propName]);
    }
    
    /**
     * _hasPropertyDefinitionOrData
     *
     * @param  mixed $propName
     * @return bool
     */
    final protected function _hasPropertyDefinitionOrData($propName): bool {
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        return isset($this->_compiledPropertyMapLink[$propName]) || array_key_exists($propName, $this->_objectData);
    }
    
    /**
     * _hasPropertyData
     *
     * @param  mixed $propName
     * @return bool
     */
    final protected function _hasPropertyData($propName): bool {
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        return array_key_exists($propName, $this->_objectData);
    }
    
    /**
     * _getPropertyDefinition
     *
     * @param  mixed $propName
     * @param  bool $allowUndefined
     * @return mixed
     */
    final protected function _getPropertyDefinition($propName, bool $allowUndefined = true): mixed {
        if(isset($this->_compiledPropertyMapLink[$propName])) {
            return $this->_compiledPropertyMapLink[$propName];
        }elseif($allowUndefined && array_key_exists($propName, $this->_objectData)) {
            return UndefinedProperty::getInstance();
        }else{
            throw new LazyJsonMapperException(sprintf(
                'No such object property "%s".',
                $propName
            ));
        }
    }
    
    /**
     * _getProperty
     *
     * @param  mixed $propName
     * @param  bool $createMissingValue
     * @return mixed
     */
    final protected function &_getProperty($propName, bool $createMissingValue = false): mixed {
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        $propDef = $this->_getPropertyDefinition($propName);
        if(array_key_exists($propName, $this->_objectData)) {
            $value = &$this->_objectData[$propName];
        }elseif($createMissingValue) {
            $this->_objectData[$propName] = null;
            $value = &$this->_objectData[$propName];
            return $value;
        }else{
            $value = null;
            return $value;
        }
        ValueConverter::convert(
            ValueConverter::CONVERT_FROM_INTERNAL,
            $value, $propDef->arrayDepth, $propName, $propDef
        );
        return $value;
    }
    
    /**
     * _isProperty
     *
     * @param  mixed $propName
     * @return bool
     */
    final protected function _isProperty($propName): bool {
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        return array_key_exists($propName, $this->_objectData) && (bool)$this->_objectData[$propName];
    }

    /**
     * _setProperty
     *
     * @param  mixed $propName
     * @param  mixed $value
     * @return DelayedJsonMapper
     */
    final protected function _setProperty($propName, mixed $value): DelayedJsonMapper {
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        $propDef = $this->_getPropertyDefinition($propName);
        ValueConverter::convert(
            ValueConverter::CONVERT_TO_INTERNAL,
            $value, $propDef->arrayDepth, $propName, $propDef
        );
        $this->_objectData[$propName] = $value;
        return $this;
    }
    
    /**
     * _unsetProperty
     *
     * @param  mixed $propName
     * @return DelayedJsonMapper
     */
    final protected function _unsetProperty($propName): DelayedJsonMapper {
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        unset($this->_objectData[$propName]);
        return $this;
    }
    
    /**
     * __call
     *
     * @param  string $functionName
     * @param  array $arguments
     * @return mixed
     */
    final public function __call(string $functionName, array $arguments): mixed {
        if (!static::ALLOW_VIRTUAL_FUNCTIONS) {
            // throw new LazyUserOptionException(
            //     $this,
            //     LazyUserOptionException::ERR_VIRTUAL_FUNCTIONS_DISABLED
            // );
            return null;
        }
        list($functionType, $funcCase) = FunctionTranslation::splitFunctionName($functionName);
        if($functionType === null) {
            throw new LazyJsonMapperException(sprintf(
                'Unknown function "%s".',
                $functionName
            ));
        }
        if(static::USE_MAGIC_LOOKUP_CACHE && isset(self::$_magicLookupCache[$funcCase])) {
            $translation = self::$_magicLookupCache[$funcCase];
        }else{
            try{
                $translation = new FunctionTranslation($funcCase);
            }catch(MagicTranslationException $e) {
                throw new LazyJsonMapperException(sprintf(
                    'Unknown function "%s".', $functionName
                ));
            }
            if(static::USE_MAGIC_LOOKUP_CACHE) {
                self::$_magicLookupCache[$funcCase] = $translation;
            }
        }
        if($this->_hasPropertyDefinitionOrData($translation->snakePropName)) {
            $propName = $translation->snakePropName;
        }elseif($translation->camelPropName !== null && $this->_hasPropertyDefinitionOrData($translation->camelPropName)) {
            $propName = $translation->camelPropName;
        }elseif($translation->dotPropName !== null && $this->_hasPropertyDefinitionOrData($translation->dotPropName)) {
            $propName = $translation->dotPropName;
        }else{
            if($functionType === 'has') {
                return false;
            }else{
                throw new LazyJsonMapperException(sprintf(
                    'Unknown function "%s".',
                    $functionName
                ));
            }
        }
        switch ($functionType) {
        case 'has':
            return true;
            break;
        case 'is':
            return $this->_isProperty($propName);
            break;
        case 'get':
            return $this->_getProperty($propName);
            break;
        case 'set':
            if(count($arguments) !== 1) {
                $propDef = $this->_getPropertyDefinition($propName);
                throw new LazyJsonMapperException(sprintf(
                    'Property setter requires exactly 1 argument: "%s(%s $value)".',
                    $functionName, $propDef->asString()
                ));
            }
            return $this->_setProperty($propName, $arguments[0]);
            break;
        case 'unset':
            return $this->_unsetProperty($propName);
            break;
        default:
            throw new LazyJsonMapperException(sprintf(
                'Unknown function "%s".',
                $functionName
            ));
        }
    }
    
    /**
     * __toString
     *
     * @return string
     */
    final public function __toString(): string {
        try {
            return $this->asJson();
        }catch(\Exception $e) {
            return sprintf('<%s>', $e->getMessage());
        }
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    final public function &__get(mixed $propName): mixed {
        if(!static::ALLOW_VIRTUAL_PROPERTIES) {
            // throw new LazyUserOptionException(
            //     $this,
            //     LazyUserOptionException::ERR_VIRTUAL_PROPERTIES_DISABLED
            // );
            return null;
        }
        return $this->_getProperty($propName, true);
    }
    
    /**
     * __set
     *
     * @param  mixed $propName
     * @param  mixed $value
     * @return void
     */
    final public function __set($propName, mixed $value): void {
        if (!static::ALLOW_VIRTUAL_PROPERTIES) {
            // throw new LazyUserOptionException(
            //     $this,
            //     LazyUserOptionException::ERR_VIRTUAL_PROPERTIES_DISABLED
            // );
            return;
        }
        $this->_setProperty($propName, $value);
    }
    
    /**
     * __isset
     *
     * @param  mixed $propName
     * @return bool
     */
    final public function __isset($propName): bool {
        if(!static::ALLOW_VIRTUAL_PROPERTIES) {
            // throw new LazyUserOptionException(
            //     $this,
            //     LazyUserOptionException::ERR_VIRTUAL_PROPERTIES_DISABLED
            // );
            return false;
        }
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        return isset($this->_objectData[$propName]);
    }

    final public function __unset($propName): void {
        if(!static::ALLOW_VIRTUAL_PROPERTIES) {
            // throw new LazyUserOptionException(
            //     $this,
            //     LazyUserOptionException::ERR_VIRTUAL_PROPERTIES_DISABLED
            // );
            return;
        }
        $this->_unsetProperty($propName);
    }
    
    /**
     * __serialize
     *
     * @return string
     */
    final public function __serialize() {
        if($this->_objectData instanceof DelayedResponse){
            $this->_objectData = $this->_objectData->fetch();
        }
        $objectData = $this->_objectData;
        array_walk_recursive($objectData, function (&$value) {
            if(is_object($value) && $value instanceof self) {
                $value = $value->__serialize($value);
            }
        });

        $args = func_get_args();
        $isRootObject = !isset($args[0]) || $args[0] !== $this;
        if (!$isRootObject) {
            return $objectData;
        }

        $serialized = null;
        try {
            $serialized = serialize($objectData);
        }catch(\Exception $e) {
            throw new LazySerializationException(sprintf(
                'Unexpected exception encountered while serializing a sub-object. Error: %s',
                $e->getMessage()
            ));
        }

        if(!is_string($serialized)) {
            throw new LazySerializationException(
                'The object data could not be serialized.'
            );
        }

        return $serialized;
    }
    
    /**
     * __unserialize
     *
     * @param  mixed $serialized
     * @return void
     */
    final public function __unserialize($serialized = null): void {
        $objectData = null;
        try {
            $objectData = unserialize($serialized);
        }catch(\Exception $e) {
            throw new LazySerializationException(sprintf(
                'Unexpected exception encountered while unserializing a sub-object. Error: %s',
                $e->getMessage()
            ));
        }
        if(!is_array($objectData)) {
            throw new LazySerializationException(
                'The serialized object data that you provided could not be unserialized.'
            );
        }
        $this->__construct($objectData);
    }
    
    /**
     * clearGlobalMagicLookupCache
     *
     * @return int
     */
    final public static function clearGlobalMagicLookupCache(): int {
        $lookupCount = count(self::$_magicLookupCache);
        self::$_magicLookupCache = [];
        return $lookupCount;
    }
    
    /**
     * clearGlobalPropertyMapCache
     *
     * @return int
     */
    final public static function clearGlobalPropertyMapCache(): int {
        $classCount = count(self::$_propertyMapCache->classMaps);
        self::$_propertyMapCache->clearCache();
        return $classCount;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool {
        return $this->_isProperty($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed {
        return $this->_getProperty($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        $this->_setProperty($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void {
        $this->_unsetProperty($offset);
    }
}

?>