# Robo Panthon Extension

[![CircleCI](https://circleci.com/gh/FloeDesignTechnologies/robo-pantheon.svg?style=svg)](https://circleci.com/gh/FloeDesignTechnologies/robo-pantheon)

Robo tasks and helpers for Pantheon projects.

## Installation

```composer require floe/robo-pantheon```

## Usage

Use the trait in your RoboFile:

```php
class RoboFile extends \Robo\Tasks {
    use \Floe\Robo\Pantheon\loadTasks;
}
```

### Run Terminus command

```php
// terminus command:subcommand:subcommand param1 param2 --option1=value --option2
$this->taskTerminus('art')
  ->param('param1');
  ->param('param2');
  ->option('option1', 'value')
  ->option('option2')
  ->run();
    
    
// terminus command:subcommand:subcommand param1 param2 --option1=value --option2
$this->taskTerminus('command:subcommand:subcommand')
  ->params('param1', 'param2')
  ->options([
    'option1' => 'value',
    'option2' => true
  ])
  ->run()

// terminus command:subcommand:subcommand param1 param2 --option1=value --option2
$this->_terminus('command:subcommand:subcommand', ['param1', 'param2'], ['option1' => 'value', 'option2' => true]);
```

### Run a stack of terminus commands

```php
// terminus command:subcommand:subcommand param1 param2 --option1=value --option2 &&
// terminus command:subcommand:subcommand param1 param2 --option1=value --option2
$this->taskTerminusStack()
  ->exec('command:subcommand:subcommand', ['param1', 'param2'], ['option1' => 'value', 'option2' => true])
  ->commandSubcommandSubcommand(['param1', 'param2'], ['option1' => 'value', 'option2' => true])
  ->run()
```

