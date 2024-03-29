<?php

namespace App\Presenters;

use App\Entities\Animal;
use App\Entities\User;
use App\Forms\AnimalDeathFormFactory;
use App\Forms\AnimalFormFactory;
use App\Repositories\AnimalRepository;
use Nette;
use Nette\Http\IResponse;


class AnimalPresenter extends SecuredPresenter
{
    /** @var AnimalFormFactory @inject */
    public $animalFormFactory;

    /** @var AnimalDeathFormFactory @inject */
    public $animalDeathFormFactory;

    /** @var AnimalRepository @inject */
    public $animalRepository;

    /** @var  Animal|NULL */
    private $editingAnimal;

    public function actionCreate()
    {
        if (!$this->user->isInRole(User::ADMIN)) {
            $this->printForbiddenMessage();
            $this->redirect('Animal:');
        }
    }

    public function renderCreate()
    {
        $this['animalForm']['id']->setDisabled();
    }

    public function renderDetail($id)
    {
        $this->template->animal = $this->animalRepository->find($id);
    }

    public function renderDefault()
    {
        $this->template->animals = $this->animalRepository->findAll();
    }

    public function actionEdit($id)
    {
        if (!$this->user->isInRole(User::ADMIN)) {
            $this->printForbiddenMessage();
            $this->redirect('Animal:');
        }

        $this->editingAnimal = $this->animalRepository->find($id);

        if ( ! $this->editingAnimal) {
            $this->error('Neexistující zvíře');
        }
    }

    public function actionDelete($id)
    {
        if (!$this->user->isInRole(User::ADMIN)) {
            $this->printForbiddenMessage();
            $this->redirect('Animal:');
        }

        $species = $this->animalRepository->find($id);

        if ($species) {
            $this->animalRepository->getEntityManager()->remove($species);
            $this->animalRepository->getEntityManager()->flush();
            $this->flashMessage('Zvíře "'. $species->getName() . '" bylo úspěšně odstraněno');
        }

        $this->redirect('Animal:');
    }

    public function actionDeath($id)
    {
        $this->editingAnimal = $this->animalRepository->find($id);

        if ( ! $this->editingAnimal) {
            $this->error('Neexistující zvíře');
        }
    }

    /**
     * Edit form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentAnimalForm()
    {
        $form = $this->animalFormFactory->create($this->editingAnimal);

        $form->onSuccess[] = function (Nette\Application\UI\Form $form, $values){
            $enclosure = $this->animalRepository->saveFormData($values);
            $this->redirect('Animal:detail', $enclosure->getId());
        };

        return $form;
    }

    /**
     * Death form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentAnimalDeathForm()
    {
        $form = $this->animalDeathFormFactory->create($this->editingAnimal);

        $form->onSuccess[] = function (Nette\Application\UI\Form $form, $values){
            $enclosure = $this->animalRepository->saveDeathFormData($values);
            $this->flashMessage('Zvíře úspěšně umřelo');
            $this->redirect('Animal:detail', $enclosure->getId());
        };

        return $form;
    }
}
