<?php


namespace App\Model;



use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CsvForm
{

    /**
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"text/plain"},
     *     mimeTypesMessage = "Veuillez télécharger un fichier .csv"
     * )
     */
    private ?UploadedFile $csvFile = null;

    /**
     * @return UploadedFile|null
     */
    public function getCsvFile(): ?UploadedFile
    {
        return $this->csvFile;
    }

    /**
     * @param UploadedFile|null $csvFile
     */
    public function setCsvFile(?UploadedFile $csvFile): void
    {
        $this->csvFile = $csvFile;
    }

    /**
     * @Assert\Callback
     */
    public function validateExtensionFile(ExecutionContextInterface $context)
    {
        if ($this->csvFile->getClientOriginalExtension() != "csv") {
            $context->buildViolation('Veuillez télécharger un fichier .csv')
                ->atPath('csvFile')
                ->addViolation();
        }
    }

}