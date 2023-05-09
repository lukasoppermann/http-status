<?php

namespace Lukasoppermann\Httpstatus\languages;

use Lukasoppermann\Httpstatus\LanguageInterface;

class de implements LanguageInterface
{
    protected $httpStatus = [
      100 => 'Fortfahren',
      101 => 'Protokolländerung',
      102 => 'Verarbeiten',
      200 => 'OK',
      201 => 'Erstellt',
      202 => 'Angenommen',
      203 => 'Nicht-Authoritative Information',
      204 => 'Kein Inhalt',
      205 => 'Inhalt zurücksetzen',
      206 => 'Teilweiser Inhalt',
      207 => 'Mehrfache Zustände',
      208 => 'Bereits übermittelt',
      226 => 'IM benutzt',
      300 => 'Mehrfach verwendet',
      301 => 'Dauerhaft umgezogen',
      302 => 'Gefunden',
      303 => 'Verweis auf',
      304 => 'Nicht verändert',
      305 => 'Proxy benutzen',
      307 => 'Vorrübergehende Weiterleitung',
      308 => 'Permanente Weiterleitung',
      400 => 'Fehlerhafte Anfrage',
      401 => 'Nicht authorisiert',
      402 => 'Bezahlung erforderlich',
      403 => 'Verboten',
      404 => 'Nicht gefunden',
      405 => 'Methode nicht erlaubt',
      406 => 'Nicht akzeptabel',
      407 => 'Proxy Authentifizierung erforderlich',
      408 => 'Zeitüberschreitung der Anfrage',
      409 => 'Konflikt',
      410 => 'Nicht mehr vorhanden',
      411 => 'Länge erforderlich',
      412 => 'Vorbedingung fehlgeschlagen',
      413 => 'Inhalt zu groß',
      414 => 'URI zu lang',
      415 => 'Nicht unterstützer Medientyp',
      416 => 'Bereich nicht adressierbar',
      417 => 'Expectation fehlgeschlagen',
      418 => 'Ich bin ein Teekessel',
      421 => 'Misdirected Request',
      422 => 'Nicht verarbeitbare Entität',
      423 => 'Gesperrt',
      424 => 'Fehlgeschlagene Abhängigkeit',
      425 => 'Zu früh',
      426 => 'Upgrade notwendig',
      428 => 'Vorbedingung vorausgesetzt',
      429 => 'Zu viele Anfragen',
      431 => 'Header Felder sind zu groß',
      451 => 'Nicht erreichbar aus rechtlichen Gründen',
      500 => 'Interner Server Fehler',
      501 => 'Nicht implementiert',
      502 => 'Kein Endpunkt (Serverfehler)',
      503 => 'Dienst nicht erreichbar',
      504 => 'Zeitüberschreitung beim Endpunkt',
      505 => 'HTTP Version nicht unterstützt',
      506 => 'Endpunkt verhandelt selbst',
      507 => 'Unzureichender Speicher',
      508 => 'Schleife entdeckt',
      510 => 'Nicht erweitert',
      511 => 'Netzwerkauthentifizierung erforderlich',
    ];

    /**
     * {@inheritdoc}
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }
}
