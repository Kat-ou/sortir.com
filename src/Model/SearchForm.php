<?php


namespace App\Model;


use App\Entity\Campus;


/**
 * Class SearchForm représentant les valeurs du formulaire de recherche des sorties selon les saisies utilisateurs.
 * @package App\Model
 */
class SearchForm
{

    private ?Campus $campus = null;
    private ?string $searchInputText = null;
    private ?\DateTime $startDate = null;
    private ?\DateTime $endDate = null;
    private ?bool $isItMeOrganizer;
    private ?bool $isItMeRegister;
    private ?bool $isItMeNoRegister;
    private ?bool $isItEventsDone;


    /**
     * Méthode retournant un tableau d'erreurs.
     * Une erreur si les 4 types de recherche sont dé-cochés.
     * Une erreur si les 2 dates ne sont pas saisies.
     * @return array
     */
    public function getErrorsSearchForm(): array {
        $result = [];
        if ($this->isItMeOrganizer === false && $this->isItMeRegister === false && $this->isItMeNoRegister === false && $this->isItEventsDone === false ) {
            array_push($result, "Sélectionnez au moins un type de sortie dont vous voulez faire la recherche.");
        }
        if ($this->startDate === null || $this->endDate === null) {
            array_push($result, "Sélectionnez les deux dates de votre intervalle de recherche.");
        }
        return $result;
    }


    /**
     * @return Campus
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     */
    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return string
     */
    public function getSearchInputText(): ?string
    {
        return $this->searchInputText;
    }

    /**
     * @param string $searchInputText
     */
    public function setSearchInputText(?string $searchInputText): void
    {
        $this->searchInputText = $searchInputText;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(?\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(?\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return bool
     */
    public function isItMeOrganizer(): ?bool
    {
        return $this->isItMeOrganizer;
    }

    /**
     * @param bool $isItMeOrganizer
     */
    public function setIsItMeOrganizer(?bool $isItMeOrganizer): void
    {
        $this->isItMeOrganizer = $isItMeOrganizer;
    }

    /**
     * @return bool
     */
    public function isItMeRegister(): ?bool
    {
        return $this->isItMeRegister;
    }

    /**
     * @param bool $isItMeRegister
     */
    public function setIsItMeRegister(?bool $isItMeRegister): void
    {
        $this->isItMeRegister = $isItMeRegister;
    }

    /**
     * @return bool
     */
    public function isItMeNoRegister(): ?bool
    {
        return $this->isItMeNoRegister;
    }

    /**
     * @param bool $isItMeNoRegister
     */
    public function setIsItMeNoRegister(?bool $isItMeNoRegister): void
    {
        $this->isItMeNoRegister = $isItMeNoRegister;
    }

    /**
     * @return bool
     */
    public function isItEventsDone(): ?bool
    {
        return $this->isItEventsDone;
    }

    /**
     * @param bool $isItEventsDone
     */
    public function setIsItEventsDone(?bool $isItEventsDone): void
    {
        $this->isItEventsDone = $isItEventsDone;
    }


}