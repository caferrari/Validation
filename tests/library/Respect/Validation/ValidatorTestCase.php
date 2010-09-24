<?php

namespace Respect\Validation;

use Mockery;

abstract class ValidatorTestCase extends \PHPUnit_Framework_TestCase
{

    protected function assertValidatorPresence($validator)
    {
        $args = func_get_args();
        array_shift($args);
        foreach ($args as $req) {
            $this->assertTrue(
                $validator->hasValidator('Respect\Validation\Foo\\' . $req)
            );
        }
    }

    protected function buildMockValidator($name, array $messages, $invalid=false)
    {
        $validator = Mockery::mock('Respect\Validation\Validatable');
        if ($invalid) {
            $validator->shouldReceive('validate')->andThrow(
                new InvalidException('Always invalid, man.')
            );
            $validator->shouldReceive('isValid')->andReturn(false);
        } else {
            $validator->shouldReceive('isValid')->andReturn(true);
            $validator->shouldReceive('validate')->andReturn(true);
        }
        $validator->shouldReceive('getMessages')->andReturn(
            $messages
        );
        $className = 'Respect\Validation\Foo\\' . $name;
        if (!class_exists($className, false)) {
            eval("
                namespace Respect\Validation\Foo; 
                class $name implements \Respect\Validation\Validatable {
                    public function validate(\$input) {}
                    public function isValid(\$input) {}
                    public function setMessages(array \$messages) {}
                    public function getMessages() {
                        return " . var_export($messages,
                    true) . ";
                    }
                } 
                ");
        }

        return $validator;
    }

    public function providerForMockImpossibleValidators()
    {
        $firstValidator = $this->buildMockValidator(
                'Bar', array('Bar_1' => 'fga', 'Bar_2' => 'dfgb'), true
        );
        $secondValidator = $this->buildMockValidator(
                'Baz', array('Baz_1' => 'gedg', 'Baz_2' => 'rihg49'), true
        );
        $thirdValidator = $this->buildMockValidator(
                'Bat', array('Bat_1' => 'dfdsgdgfgb'), true
        );
        return array(
            array($firstValidator, $secondValidator, $thirdValidator),
            array($secondValidator, $firstValidator, $thirdValidator),
            array($thirdValidator, $secondValidator, $firstValidator)
        );
    }

    public function providerForMockValidators()
    {
        $firstValidator = $this->buildMockValidator(
                'Bara', array('Bara_1' => 'fga', 'Bara_2' => 'dfgb'), false
        );
        $secondValidator = $this->buildMockValidator(
                'Baza', array('Baza_1' => 'gedg', 'Baza_2' => 'rihg49'), false
        );
        $thirdValidator = $this->buildMockValidator(
                'Bata', array('Bata_1' => 'dfdsgdgfgb'), false
        );
        return array(
            array($firstValidator, $secondValidator, $thirdValidator),
            array($secondValidator, $firstValidator, $thirdValidator),
            array($thirdValidator, $secondValidator, $firstValidator)
        );
    }

}