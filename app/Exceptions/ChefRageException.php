<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Wird geworfen, wenn die KI nicht (sinnvoll) antwortet.
 *
 * Die Nachricht ist bewusst nutzerfreundlich und im Ton des Chefkochs
 * formuliert, sie darf direkt im Frontend angezeigt werden. Technische
 * Details gehören ins Log, nicht in diese Nachricht.
 */
class ChefRageException extends RuntimeException
{
    public const LEFT_KITCHEN = 'Der Chefkoch hat vor Wut die Küche verlassen. Bitte versuche es noch einmal.';

    public const TOO_SLOW = 'Der Chefkoch hat zu lange auf deine Zutaten gestarrt und ist eingeschlafen. Bitte versuche es noch einmal.';

    public const OVERRUN = 'Die Küche ist gerade überfüllt. Der Chef nimmt keine Bestellungen an. Versuch es in einer Minute noch einmal.';

    public const GIBBERISH = 'Der Chefkoch hat nur noch unverständlich auf Französisch geflucht. Bitte versuche es noch einmal.';

    public const CLOSED = 'Die Küche ist heute geschlossen. Der Chef wartet noch auf seine Zugangsdaten.';

    public const REFUSED = 'Der Chefkoch weigert sich, damit zu kochen. Versuch es mit echten Lebensmitteln.';

    public static function leftKitchen(?\Throwable $previous = null): self
    {
        return new self(self::LEFT_KITCHEN, 0, $previous);
    }

    public static function tooSlow(?\Throwable $previous = null): self
    {
        return new self(self::TOO_SLOW, 0, $previous);
    }

    public static function overrun(?\Throwable $previous = null): self
    {
        return new self(self::OVERRUN, 0, $previous);
    }

    public static function gibberish(?\Throwable $previous = null): self
    {
        return new self(self::GIBBERISH, 0, $previous);
    }

    public static function closed(): self
    {
        return new self(self::CLOSED);
    }

    public static function refused(): self
    {
        return new self(self::REFUSED);
    }
}
