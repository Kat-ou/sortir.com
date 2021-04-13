<?php


namespace App\Services;


/**
 * Class NameState représentant les noms d'état réelle des sorties présentent en base de données.
 * @package App\Services
 */
abstract class NameState
{

    public const STATE_CREATED = 'Créée';
    public const STATE_DONE = 'Passée';
    public const STATE_CANCELED = 'Annulée';
    public const STATE_HISTORIZED = 'Historisée';
    public const STATE_OPEN = 'Ouverte';
    public const STATE_END_REGISTER = 'Clôturée';
    public const STATE_IN_PROGRESS = 'Activité en cours';

}