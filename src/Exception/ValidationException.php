<?php

declare(strict_types=1);

namespace App\Library\Exception;

use Exception;

/**
 * Validation Exception
 *
 * Thrown when input validation fails or required parameters are invalid.
 *
 * @author Library Developer
 * @since 2026-05-09
 */
class ValidationException extends Exception
{