<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

const REQUEST_ID_KEY = "CSRF-PROTECTION-TOKEN";

const FORBIDDEN_REQUEST_MESSAGE = "%s A request has been blocked";

const TOKEN_MAX = 10;

const TOKEN_DEFAULT_TTL = 1800;
