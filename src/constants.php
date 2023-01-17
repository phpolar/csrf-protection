<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

const REQUEST_ID_KEY = "X_CSRF_TOKEN";

const FORBIDDEN_REQUEST_MESSAGE = "%s A request has been blocked";