<?php

namespace Lukasoppermann\Httpstatus\languages;

use Lukasoppermann\Httpstatus\LanguageInterface;

class fr implements LanguageInterface
{
    // Sources:
    // - https://assiste.com/Codes_HTTP/101.html
    // - http://www.adrhi.fr/utilitaires/Codes-HTTP.html
    protected $httpStatus = [
      100 => 'Continuer',
      101 => 'Changement de protocole',
      102 => 'En traitement',
      200 => 'OK',
      201 => 'Crée',
      202 => 'Accepté',
      203 => 'Information non certifiée',
      204 => 'Pas de contenu',
      205 => 'Contenu réinitialisé',
      206 => 'Contenu partiel',
      207 => 'Multi-Status',
      208 => 'Déjà signalé',
      226 => 'IM Used',
      300 => 'Choix multiples',
      301 => 'Changement d\'adresse définitif',
      302 => 'Changement d\'adresse temporaire',
      303 => 'Voir ailleurs',
      304 => 'Non modifié',
      305 => 'Utiliser le proxy',
      307 => 'Redirection temporaire',
      308 => 'Redirection définitive',
      400 => 'Mauvaise requête',
      401 => 'Non autorisé',
      402 => 'Paiement exigé',
      403 => 'Interdit',
      404 => 'Non trouvé',
      405 => 'Méthode non autorisée',
      406 => 'Aucun disponible',
      407 => 'Authentification proxy exigée',
      408 => 'Requête hors-délai',
      409 => 'Conflit',
      410 => 'Parti',
      411 => 'Longueur exigée',
      412 => 'Précondition échouée',
      413 => 'Corps de requête trop grand',
      414 => 'URI trop long',
      415 => 'Format non supporté',
      416 => 'Plage demandée invalide',
      417 => 'Comportement erroné',
      418 => 'Je suis une théière',
      421 => 'Demande mal adressée',
      422 => 'Entité non traitable',
      423 => 'Vérouillé',
      424 => 'Échec de la méthode',
      425 => 'Collection non ordonnée',
      426 => 'Mise à niveau requise',
      428 => 'Condition préalable requise',
      429 => 'Trop de requêtes',
      431 => 'Entête de la requête HTTP trop grande',
      451 => 'Inaccessible pour des raisons d\'ordre légal',
      500 => 'Erreur interne du serveur',
      501 => 'Non implémenté',
      502 => 'Mauvais intermédiaire ou erreur proxy',
      503 => 'Service indisponible',
      504 => 'Intermédiaire hors-délai',
      505 => 'Version HTTP non supportée',
      506 => 'Référence circulaire',
      507 => 'Espace insuffisant',
      508 => 'Boucle détectée',
      510 => 'Non prolongé',
      511 => 'Authentification réseau requise',
    ];

    /**
     * {@inheritdoc}
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }
}
