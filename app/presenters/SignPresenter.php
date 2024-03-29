<?php

namespace App\Presenters;

use Nette;
use App\Forms;


class SignPresenter extends BasePresenter
{
	/** @var Forms\SignInFormFactory @inject */
	public $signInFactory;

	/** @var Forms\SignUpFormFactory @inject */
	public $signUpFactory;


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->signInFactory->create();

        $form->onSuccess[] = function () {
            $this->redirect('Homepage:');
		};

		return $form;
	}


	/**
	 * Sign-up form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignUpForm()
	{
		$form = $this->signUpFactory->create();

        $form->onSuccess[] = function () {
            $this->flashMessage('Registrace proběhla úspěšně, můžete se přihlásit');
            $this->redirect('Sign:in');
        };

        return $form;
	}


	public function actionOut()
	{
		$this->getUser()->logout();
	}

}
