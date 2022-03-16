#!/usr/bin/env php
<?php

require_once __DIR__ . "/../autoload.php";

use FluxMailRestApi\Adapter\Server\MailRestApiServer;

MailRestApiServer::new()
    ->init();
