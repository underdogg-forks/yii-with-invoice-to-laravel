<?php

namespace App\Enums;

enum PeppolProvider: string
{
    case STORECOVE = 'storecove';
    case LETSPEPPOL = 'letspeppol';
    case PEPPYRUS = 'peppyrus';
    case EINVOICING_BE = 'einvoicing_be';

    public function getBaseUrl(string $environment = 'production'): string
    {
        return match($this) {
            self::STORECOVE => $environment === 'sandbox' 
                ? 'https://api-sandbox.storecove.com/api/v2'
                : 'https://api.storecove.com/api/v2',
            self::LETSPEPPOL => 'https://api.letspeppol.com/v1',
            self::PEPPYRUS => 'https://api.peppyrus.com/v1',
            self::EINVOICING_BE => 'https://api.e-invoicing.be/v1',
        };
    }

    public function getName(): string
    {
        return match($this) {
            self::STORECOVE => 'StoreCove',
            self::LETSPEPPOL => 'LetsPeppol',
            self::PEPPYRUS => 'Peppyrus',
            self::EINVOICING_BE => 'E-invoicing.be',
        };
    }
}
