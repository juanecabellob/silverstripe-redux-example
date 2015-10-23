<?php

use LittleGiant\SilverStripeSeeder\Providers\Provider;

/**
 * Class RandomObjectProvider
 */
class RandomObjectProvider extends Provider
{
    /**
     * @var string
     */
    public static $shorthand = 'RandomObject';

    /**
     * @param $argumentString
     * @return array
     */
    public static function parseOptions($argumentString)
    {
        $args = array_map(function ($arg) {
            return trim($arg);
        }, explode(',', $argumentString));

        $options = array(
            'class' => $args[0],
        );

        if (count($options) > 1) {
            $options['count'] = $options[1];
        }

        return $options;
    }

    /**
     * @param $field
     * @param $state
     * @throws Exception
     * @returns null
     */
    protected function generateField($field, $state)
    {
        throw new Exception('object provider does not support generating db fields');
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     * @throws Exception
     */
    protected function generateHasOneField($field, $state)
    {
        if (empty($field->arguments['class'])) {
            throw new Exception('object provider requires a \'class\'');
        }
        if (!class_exists($field->arguments['class'])) {
            throw new Exception("class '{$field->arguments['class']}' does not exist");
        }

        $className = $field->arguments['class'];
        $object = $className::get()->sort('RAND()')->first();

        if (!$object) {
            SS_Log::log("object for {$field->arguments['class']} not found", SS_Log::WARN);
        }

        return $object;
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     * @throws Exception
     */
    protected function generateHasManyField($field, $state)
    {
        if (empty($field->arguments['class'])) {
            throw new Exception('object provider requires a \'class\'');
        }
        if (!class_exists($field->arguments['class'])) {
            throw new Exception("class '{$field->arguments['class']}' does not exist");
        }

        // error checking
        $className = $field->arguments['class'];
        $objects = $className::get()->sort('RAND()')->limit($field->count)->toArray();
        return $objects;
    }

    /**
     * @param $field
     * @param $state
     * @return mixed
     * @throws Exception
     */
    protected function generateManyManyField($field, $state)
    {
        return $this->generateHasManyField($field, $state);
    }
}
