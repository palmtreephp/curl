<?php

$request = \file_get_contents('php://input');

$json = \json_decode($request, true);

\var_export($json['foo'] === true);
