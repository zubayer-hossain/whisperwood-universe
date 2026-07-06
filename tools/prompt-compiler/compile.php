#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Whisperwood\PromptCompiler\CommandRouter;
use Whisperwood\PromptCompiler\PathResolver;

$router = new CommandRouter(new PathResolver(__DIR__));

exit($router->run($argv));
