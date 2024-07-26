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

To create a form using PHP 8 attributes, first you have to extend [AttributeForm](src/AttributeForm.php).

Then declare all input elements and buttons as property :
- For element : `public|protected|private MyElementType $myElementName;`
- For button : `public|protected|private ButtonInterface $myButton;`

Finally, use attributes on properties (or form class) for configure elements, add constraints, transformers...

```php
#[Positive, UnitTransformer, GetSet]
public IntegerElement $weight;
```

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

### Supported attributes

This library supports various attributes types for configure form elements :

- [Symfony validator's](https://github.com/symfony/validator) `Constraint`, translated as `...->satisfy(new Constraint(...))`
- [`ExtractorInterface`](https://github.com/b2pweb/bdf-form/blob/master/src/PropertyAccess/ExtractorInterface.php), translated as `...->extractor(new Extractor(...))`
- [`HydratorInterface`](https://github.com/b2pweb/bdf-form/blob/master/src/PropertyAccess/HydratorInterface.php), translated as `...->hydrator(new Hydrator(...))`
- [`FilterInterface`](https://github.com/b2pweb/bdf-form/blob/master/src/Filter/FilterInterface.php), translated as `...->filter(new Filter(...))`
- [`TransformerInterface`](https://github.com/b2pweb/bdf-form/blob/master/src/Transformer/TransformerInterface.php), translated as `...->transformer(new Transformer(...))`

### Generate the configurator code from attributes

To improve performance, and to do without the use of reflection, attributes can be used to generate the PHP code
of the configurator, instead of dynamically configure the form.

To do that, use [`CompileAttributesProcessor`](src/Processor/CompileAttributesProcessor.php) as argument of form constructor.

```php
const GENERATED_NAMESPACE = 'Generated\\';
const GENERATED_DIRECTORY = __DIR__ . '/var/generated/form/';

// Configure the processor by setting class and file resolvers
$processor = new CompileAttributesProcessor(
    fn (AttributeForm $form) => GENERATED_NAMESPACE . get_class($form) . 'Configurator', // Retrieve the configurator class name from the form object
    fn (string $className) => GENERATED_DIRECTORY . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php', // Get the filename of the configurator class from the configurator class name
);

$form = new MyForm(processor: $processor); // Set the processor on the constructor
$form->submit(['firstName' => 'John']); // Directly use the form : the configurator will be automatically generated

// You can also pre-generate the form configurator using CompileAttributesProcessor::generate()
$processor->generate(new MyOtherForm());
```

## Available attributes

### On form class

| Attribute                                             | Example                         | Translated to                              | Purpose                                                                                                        |
|-------------------------------------------------------|---------------------------------|--------------------------------------------|----------------------------------------------------------------------------------------------------------------|
| [`Generates`](src/Form/Generates.php)                 | `Generates(MyEntity::class)`    | `$builder->generates(MyEntity::class)`     | Define the entity class generated by the form.                                                                 |
| [`CallbackGenerator`](src/Form/CallbackGenerator.php) | `CallbackGenerator('generate')` | `$builder->generates([$this, 'generate'])` | Define the method to use for generate the form value. The method must be declared as public on the form class. |
| [`Csrf`](src/Form/Csrf.php)                           | `Csrf(tokenId: 'MyToken')`      | `$builder->csrf()->tokenId('MyToken')`     | Add a CSRF element on the form.                                                                                |

### On method

| Attribute                                                  | Example                              | Translated to                                         | Purpose                                                     |
|------------------------------------------------------------|--------------------------------------|-------------------------------------------------------|-------------------------------------------------------------|
| [`AsConstraint`](src/Constraint/AsConstraint.php)          | `AsConstraint('validateFoo')`        | `$builder->satisfy([$this, 'validateFoo'])`           | Use the method as constraint for the target element.        |
| [`AsArrayConstraint`](src/Aggregate/AsArrayConstraint.php) | `AsArrayConstraint('validateFoo')`   | `$builder->arrayConstraint([$this, 'validateFoo'])`   | Use the method as constraint for the target array element.  |
| [`AsFilter`](src/Child/AsFilter.php)                       | `AsFilter('filterFoo')`              | `$builder->filter([$this, 'filterFoo'])`              | Use the method as filter for the target element.            |
| [`AsTransformer`](src/Element/AsTransformer.php)           | `AsTransformer('transformFoo')`      | `$builder->transformer([$this, 'transformFoo'])`      | Use the method as HTTP transformer for the target element.  |
| [`AsModelTransformer`](src/Child/AsModelTransformer.php)   | `AsModelTransformer('transformFoo')` | `$builder->modelTransformer([$this, 'transformFoo'])` | Use the method as model transformer for the target element. |

### On button property

| Attribute                         | Example                | Translated to                 | Purpose                                                           |
|-----------------------------------|------------------------|-------------------------------|-------------------------------------------------------------------|
| [`Groups`](src/Button/Groups.php) | `Groups('foo', 'bar')` | `...->groups(['foo', 'bar'])` | Define validation groups to use when the given button is clicked. |
| [`Value`](src/Button/Value.php)   | `Value('foo')`         | `...->value('foo')`           | Define the button value.                                          |

### On element property

| Attribute                                                                  | Example                                                                  | Translated to                                                                                                                               | Purpose                                                                                                                                                                               |
|----------------------------------------------------------------------------|--------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **Child**                                                                  |                                                                          |                                                                                                                                             |                                                                                                                                                                                       |
| [`ModelTransformer`](src/Child/ModelTransformer.php)                       | `ModelTransformer(MyTransformer::class, ['ctroarg'])`                    | `...->modelTransformer(new MyTransformer('ctorarg))`                                                                                        | Define a [model transformer](https://github.com/b2pweb/bdf-form#10---apply-model-transformer-scope-child) on the current child.                                                       |
| [`CallbackModelTransformer`](src/Child/CallbackModelTransformer.php)       | `CallbackModelTransformer(toEntity: 'parseInput', toInput: 'normalize')` | `...->modelTransformer(fn ($value, $input, $toEntity) => $toEntity ? $this->parseInput($value, $input) : $this->normalize($value, $input))` | Define a [model transformer](https://github.com/b2pweb/bdf-form#10---apply-model-transformer-scope-child) using a form method.                                                        |
| [`Configure`](src/Child/Configure.php)                                     | `Configure('configureInput')`                                            | `...->configureField($elementBuilder)`                                                                                                      | Manually configure the element builder using a form method. The method must be public and declared on the form class.                                                                 |
| [`DefaultValue`](src/Child/DefaultValue.php)                               | `DefaultValue(42)`                                                       | `...->configureField($elementBuilder)`                                                                                                      | Define the default value of the input.                                                                                                                                                |
| [`Dependencies`](src/Child/Dependencies.php)                               | `Dependencies('foo', 'bar')`                                             | `...->depends('foo', 'bar')`                                                                                                                | Declare dependencies on the current input. Dependencies will be submitted before the current field.                                                                                   |
| [`GetSet`](src/Child/GetSet.php)                                           | `GetSet('realField')`                                                    | `...->getter('realField')->setter('realField')`                                                                                             | Enable hydration and extraction of the entity.                                                                                                                                        |
| [`CallbackFilter`](src/Child/CallbackFilter.php)                           | `CallbackFilter('filterMethod')`                                         | `...->filter([$this, 'filterMethod'])`                                                                                                      | Add a filter on the current child using a method.                                                                                                                                     |
| [`HttpField`](src/Child/HttpField.php)                                     | `HttpField('_field')`                                                    | `...->httpField(new ArrayOffsetHttpField('_field'))`                                                                                        | Define the http field name to use on the current child, instead of use the property name.                                                                                             |
| **Element**                                                                |                                                                          |                                                                                                                                             |                                                                                                                                                                                       |
| [`CallbackConstraint`](src/Constraint/CallbackConstraint.php)              | `CallbackConstraint('validateInput')`                                    | `...->satisfy([$this, 'validateInput'])`                                                                                                    | Validate an input using a method.                                                                                                                                                     |
| [`Satisfy`](src/Constraint/Satisfy.php)                                    | `Satisfy(MyConstraint::class, ['opt' => 'val'])`                         | `...->satisfy(new MyConstraint(['opt' => 'val']))`                                                                                          | Add a constraint on the input. Prefer directly use the constraint class as attribute if possible.                                                                                     |
| [`Transformer`](src/Element/Transformer.php)                               | `Transformer(MyTransformer::class, ['ctorarg'])`                         | `...->transformer(new MyTransformer('ctorarg))`                                                                                             | Add a [transformer](https://github.com/b2pweb/bdf-form#6---call-element-transformers-scope-element) on the input. Prefer directly use the transformer class as attribute if possible. |
| [`CallbackTransformer`](src/Element/CallbackTransformer.php)               | `CallbackTransformer(fromHttp: 'parse', toHttp: 'stringify')`            | `...->transformer(fn ($value, $input, $toPhp) => $toPhp ? $this->parse($value, $input) : $this->stringify($value, $input))`                 | Add a [transformer](https://github.com/b2pweb/bdf-form#6---call-element-transformers-scope-element) using a form method.                                                              |
| [`Choices`](src/Element/Choices.php)                                       | `Choices(['foo', 'bar'])`                                                | `...->choices(['foo', 'bar'])`                                                                                                              | Define the values choices of the input. Supports using a method as choices provider.                                                                                                  |
| [`Raw`](src/Element/Raw.php)                                               | `Raw`                                                                    | `...->raw()`                                                                                                                                | For number elements. Use native PHP cast instead of locale parsing for convert number.                                                                                                |
| [`TransformerError`](src/Element/TransformerError.php)                     | `TransformerError(message: 'Invalid value provided')`                    | `...->transformerErrorMessage('Invalid value provided')`                                                                                    | Configure error handling of transformer exceptions.                                                                                                                                   |
| [`IgnoreTransformerException`](src/Element/IgnoreTransformerException.php) | `IgnoreTransformerException`                                             | `...->ignoreTransformerException()`                                                                                                         | Ignore transformer exception. If enable and an exception occurs, the raw value will be used.                                                                                          |
| [`Required`](src/Element/Required.php)                                     | `Required`                                                               | `...->required()`                                                                                                                           | Mark the element as required. The error message can be defined as parameter of the attribute.                                                                                         |
| **DateTimeElement**                                                        |                                                                          |                                                                                                                                             |                                                                                                                                                                                       |
| [`DateFormat`](src/Element/Date/DateFormat.php)                            | `DateFormat('d/m/Y H:i')`                                                | `...->format('d/m/Y H:i')`                                                                                                                  | Define the input date format.                                                                                                                                                         |
| [`DateTimeClass`](src/Element/Date/DateTimeClass.php)                      | `DateTimeClass(Carbon::class)`                                           | `...->className(Carbon::class)`                                                                                                             | Define date time class to use on for parse the date.                                                                                                                                  |
| [`ImmutableDateTime`](src/Element/Date/ImmutableDateTime.php)              | `ImmutableDateTime`                                                      | `...->immutable()`                                                                                                                          | Use `DateTimeImmutable` as date time class.                                                                                                                                           |
| [`Timezone`](src/Element/Date/Timezone.php)                                | `Timezone('Europe/Paris')`                                               | `...->timezone('Europe/Paris')`                                                                                                             | Define the parsing and normalized timezone to use.                                                                                                                                    |
| [`AfterField`](src/Element/Date/AfterField.php)                            | `AfterField('otherField')`                                               | `...->afterField('otherField')`                                                                                                             | Add a greater than an other field constraint to the current element.                                                                                                                  |
| [`BeforeField`](src/Element/Date/BeforeField.php)                          | `BeforeField('otherField')`                                              | `...->beforeField('otherField')`                                                                                                            | Add a less than an other field constraint to the current element.                                                                                                                     |
| **ArrayElement**                                                           |                                                                          |                                                                                                                                             |                                                                                                                                                                                       |
| [`ArrayConstraint`](src/Aggregate/ArrayConstraint.php)                     | `ArrayConstraint(MyConstraint::class, ['opt' => 'val'])`                 | `...->arrayConstraint(new MyConstraint(['opt' => 'val']))`                                                                                  | Add a constraint on the whole array element.                                                                                                                                          |
| [`CallbackArrayConstraint`](src/Aggregate/CallbackArrayConstraint.php)     | `CallbackArrayConstraint('validateInput')`                               | `...->arrayConstraint([$this, 'validateInput'])`                                                                                            | Add a constraint on the whole array element, using a form method.                                                                                                                     |
| [`Count`](src/Aggregate/Count.php)                                         | `Count(min: 3, max: 6)`                                                  | `...->arrayConstraint(new Count(min: 3, max: 6))`                                                                                           | Add a Count constraint on the array element.                                                                                                                                          |
| [`ElementType`](src/Aggregate/ElementType.php)                             | `ElementType(IntegerElement::class, 'configureElement')`                 | `...->element(IntegerElement::class, [$this, 'configureElement'])`                                                                          | Define the array element type. A configuration callback method can be define for configure the inner element.                                                                         |
| [`ArrayTransformer`](src/Aggregate/ArrayTransformer.php)                   | `ArrayTransformer(MyTransformer::class, ['ctroarg'])`                    | `...->arrayTransformer(new MyTransformer('ctorarg))`                                                                                        | Add a transformer for the whole array input.                                                                                                                                          |
