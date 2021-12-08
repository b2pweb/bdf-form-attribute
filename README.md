# BDF Form attribute

[![build](https://github.com/b2pweb/bdf-form-attribute/actions/workflows/php.yml/badge.svg)](https://github.com/b2pweb/bdf-form-attribute/actions/workflows/php.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/b2pweb/bdf-form-attribute/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/b2pweb/bdf-form-attribute/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/b2pweb/bdf-form-attribute/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/b2pweb/bdf-form-attribute/?branch=master)
[![Packagist Version](https://img.shields.io/packagist/v/b2pweb/bdf-form-attribute.svg)](https://packagist.org/packages/b2pweb/bdf-form-attribute)
[![Total Downloads](https://img.shields.io/packagist/dt/b2pweb/bdf-form-attribute.svg)](https://packagist.org/packages/b2pweb/bdf-form-attribute)
[![Type Coverage](https://shepherd.dev/github/b2pweb/bdf-form-attribute/coverage.svg)](https://shepherd.dev/github/b2pweb/bdf-form-attribute)

Declaring forms using PHP 8 attributes and typed properties, over [BDF form](https://github.com/b2pweb/bdf-form)

## Usage

### Install using composer

```
composer require b2pweb/bdf-form-attribute
```

### Declare a form class

> Adaptation of example from BDF Form : [Handle entities](https://github.com/b2pweb/bdf-form#handle-entities)

```php

use Bdf\Form\Attribute\Form\Generates;
use Bdf\Form\Leaf\StringElement;
use Symfony\Component\Validator\Constraints\NotBlank;
use Bdf\Form\Attribute\Child\GetSet;
use Bdf\Form\Attribute\AttributeForm;
use Bdf\Form\Leaf\Date\DateTimeElement;
use Bdf\Form\Attribute\Element\Date\ImmutableDateTime;
use Bdf\Form\Attribute\Child\CallbackModelTransformer;
use Bdf\Form\ElementInterface;

// Declare the entity
class Person
{
    public string $firstName;
    public string $lastName;
    public ?DateTimeInterface $birthDate;
    public ?Country $country;
}

#[Generates(Person::class)] // Define that PersonForm::value() should return a Person instance
class PersonForm extends AttributeForm // The form must extend AttributeForm to use PHP 8 attributes syntax
{
    // Declare a property for declare an input on the form
    // The property type is used as element type
    // use NotBlank for mark the input as required
    // GetSet will define entity accessor
    #[NotBlank, GetSet] 
    private StringElement $firstName;

    #[NotBlank, GetSet] 
    private StringElement $lastName;

    // Use ImmutableDateTime to change the value of birthDate to DateTimeImmutable
    #[ImmutableDateTime, GetSet]
    private DateTimeElement $birthDate;

    // Custom transformer can be declared with a method name as first parameter of ModelTransformer
    // Transformers methods must be declared as public on the form class
    #[ImmutableDateTime, CallbackModelTransformer(toEntity: 'findCountry', toInput: 'extractCountryCode'), GetSet]
    private StringElement $country;
    
    // Transformer used when extracting input value from entity
    public function findCountry(Country $value, ElementInterface $element): string
    {
        return $value->code;
    }
    
    // Transformer used when filling entity with input value
    public function extractCountryCode(string $value, ElementInterface $element): ?Country
    {
        return Country::findByCode($value);
    }
}
```
