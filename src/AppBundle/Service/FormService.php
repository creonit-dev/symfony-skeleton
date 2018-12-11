<?php

namespace AppBundle\Service;

use AppBundle\Model\Base\FormFieldOptionQuery;
use AppBundle\Model\FormFieldQuery;
use AppBundle\Model\FormQuery;
use AppBundle\Model\Form;
use AppBundle\Model\FormField;
use AppBundle\Model\FormResult;
use AppBundle\Model\FormResultField;
use AppBundle\Model\Holder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class FormService
{
	protected $container;
	protected $request;
	protected $url = '/api/forms/';

	public function __construct(ContainerInterface $container, RequestStack $requestStack) {
		$this->container = $container;
		$this->request = $requestStack->getMasterRequest();
	}


	protected $row_template = '
        <label class="form__input">
            <span class="form__field">
                {field}
            </span>
        </label>';

	public function setRowTemplate($template){
		$this->row_template = $template;
	}

	public function getForm($code) {
		if($code > 0) {
			return FormQuery::create()->filterByVisible(true)->findPk($code);
		}else{
			return FormQuery::create()->filterByVisible(true)->findOneByCode($code);
		}
	}

	public function render($code, $options = []){
		$form = $code instanceof Form ? $code : $this->getForm($code);
		if(!$form) return '';
		$action = isset($options['action']) ? $options['action'] : $this->url.$form->getId().'/';
		$html = '<form method="post" js-generated-form action="'.$action.'" id="form_'.$form->getId().'">';
		if(isset($options['target']) && $options['target'] instanceof Holder){
			$html .= "<input type='hidden' name='_target' value='{$options['target']->getId()}'>";
		}

		foreach($this->getFields($form) as $item){
			$html .= $this->buildRow($item);
		}

        if(!isset($options['hide_submit'])){
            $html .= '<div class="form-actions">
                        <button type="submit" button=""><span>'.$this->container->get('translator')->trans('Отправить').'</span></button>
                    </div>';
        }

		$html .= '</form>';
		return $html;

	}



	public function renderSelectOptions($code, $id, $options = []){
		$form = $code instanceof Form ? $code : $this->getForm($code);
		if(!$form) return '';
		$field = FormFieldQuery::create()->filterByForm($form)->filterById($id)->findOne();
		if(!$field) return '';
		$field_html = '';
		foreach(FormFieldOptionQuery::create()->filterByFormField($field)->orderBySortableRank()->find() as $option){
			$field_html .= '<option value="'.$option->getId().'">'.$option->getTitle().'</option>';
		}

		return $field_html;
	}



	public function buildRow(FormField $field){
		$type = $field->getType();
		$type_name = Form::getTypeName($type);

		if($field->getType() == Form::FIELD_TEXTAREA){
			$field_html = '<textarea rows="3" cols="15" placeholder="'.$field->getTitle().'" name="'.$field->getFieldName().'" id="'.$field->getCode().'"></textarea>';
		}elseif($field->getType() == Form::FIELD_FILE){
			$field_html = '<input type="'.$type_name.'" name="'.$field->getFieldName().'" id="'.$field->getCode().'">';
		}elseif($field->getType() == Form::FIELD_SELECT){
			$field_html = '<select name="'.$field->getFieldName().'" js-selectus>';
			$field_html .= '<option value=""></option>';
			foreach(FormFieldOptionQuery::create()->filterByFormField($field)->orderBySortableRank()->find() as $option){
				$field_html .= '<option value="'.$option->getId().'">'.$option->getTitle().'</option>';
			}
			$field_html .= '</select>';
		}else{
			$value = '';
			if($this->container->get('security.token_storage')->getToken() and $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
				$user = $this->container->get('security.token_storage')->getToken()->getUser();
				if($field->getCode() == 'email'){
					$value = $user->getEmail();
				}
				if($field->getCode() == 'name'){
					$value = $user->getTitleFull();
				}
				if($field->getCode() == 'phone'){
					$value = $user->getPhone();
				}
			}

			$field_html = '<input  placeholder="'.$field->getTitle().'" '.($field->getCode() == 'phone' ? 'data-masked="phone"' : '').' '.($field->getCode() == 'date' ? ' data-inputmask=\'"mask": "d.m.y", "placeholder": "дд.мм.гггг"\'' : '').' type="'.$type_name.'" name="'.$field->getFieldName().'" id="'.$field->getCode().'" value="'.$value.'">';
		}

		$row = strtr($this->row_template, ['{field}' => $field_html, '{title}' => $field->getTitle(), '{type_name}' => $type_name]);

		return $row;
	}


	public function getFields(Form $form){
		return FormFieldQuery::create()->filterByVisible(true)->filterByFormId($form->getId())->filterByVisible(true)->orderBySortableRank()->find();
	}


	public function getConstraints($field){
		$constraints = [];
		if($field->getRequired()){
			$constraints[] = new NotBlank($field->getRequiredError() ? ['message' => $field->getRequiredError()] : null);
		}

		if($field->getValidationType()){
			$options = [];
			$regEx = false;
			if($field->getValidationType() == Form::VALIDATION_DIGITS){
				$regEx = true;
				$options = [
					'pattern' => '/^-?(?:\d+|\d*\.\d+){2}$/',
					'htmlPattern' => '/^-?(?:\d+|\d*\.\d+)$/'
				];

			}elseif($field->getValidationType() == Form::VALIDATION_EMAIL){

				if($field->getInvalidError()) $options['message'] = $field->getInvalidError();
				$constraints[] = new Email($options);
			}elseif ($field->getValidationType() == Form::VALIDATION_ALPHABETICAL){

				$regEx = true;
				$options = [
					'pattern' => '/^[a-zA-Zа-яА-Я \-]{3,50}$/ui',
					'htmlPattern' => '/^[a-zA-Zа-яА-Я \-]{3,50}$/ui'
				];

			}elseif ($field->getValidationType() == Form::VALIDATION_PHONE){
				$regEx = true;
				$options = [
					'pattern' => '/^\+\d *(\(\d{3,4}\)|\d{3,4})([ -]*\d){6,7}$/',
					'htmlPattern' => '/^\+\d *(\(\d{3,4}\)|\d{3,4})([ -]*\d){6,7}$/'
				];
			}

			if($regEx){
				if($field->getInvalidError()) $options['message'] = $field->getInvalidError();
				$constraints[] = new Regex($options);
			}


		}

		return $constraints;
	}

	public function validate(Form $form)
    {

	}

	public function saveResult(Form $form, $data, $result = null){

		$files = [];

		if(!$result){
			$result = new FormResult();
		}

		$result
			->setIpAddress($this->request->getClientIp())
			->setStatus(FormResult::STATUS_NEW)
			->setUrlFrom($data['url'])
			->setForm($form)
			->setFormTitle($form->getTitle())
		;

		if($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')){
			$result->setUserId($this->container->get('security.token_storage')->getToken()->getUser()->getId());
		}

		if(isset($data['_target'])){
			$result->setHolder($data['_target']);
		}

		$result->save();

		foreach($form->getFields() as $field){
			if(!isset($data[$field->getFieldName()])) continue;
			$resultField = new FormResultField();
			$resultField
				->setFieldId($field->getId())
				->setForm($form)
				->setResultId($result->getId())
				->setSortableRank($field->getSortableRank())
				->save();

			if($field->isFile()){
				$uploadedFile = $data[$field->getFieldName()];

                $slugifyService = $this->container->get('slugify');


				$file_name = $slugifyService->slugify($form->getTitle()) . '_' . $result->getId() . '_' . $field->getId() . '_' . date('m_d_Y_H_i_s').'.zip';
				$zipWebPath = '/uploads/form/'.$file_name;
				$zipPath = $this->container->get('kernel')->getRootDir().'/../web'.$zipWebPath;
				$zipDirPath = $this->container->get('kernel')->getRootDir().'/../web/uploads/form';

				if(!is_dir($zipDirPath)) mkdir($zipDirPath);

				$zip = new \ZipArchive();
				$zip->open($zipPath, \ZipArchive::CREATE);
				$zip->addFile($uploadedFile->getRealPath(), $slugifyService->slugify($uploadedFile->getClientOriginalName()));
				$resultField->setFilePath($zipWebPath);
				$files[] = $zipPath;

			}else{
				if($field->getType() == Form::FIELD_SELECT){
					$option = FormFieldOptionQuery::create()->filterByFieldId($field->getId())->filterById($data[$field->getFieldName()])->findOne();
					if($option){
						$resultField->setValue($option->getTitle());
					}else{
						$resultField->setValue($data[$field->getFieldName()]);
					}

				}else{
					$resultField->setValue($data[$field->getFieldName()]);
				}

			}

			$resultField->save();
		}

		return $result;

	}


	public function sendNotification(FormResult $result){

		$form = $result->getForm();
		$mailing = $this->container->get('creonit_mailing');
		$notification_email = $form->getNotificationEmail();

		if($notification_email){
			$message = $mailing->createMessage($result->getFormTitle(), ['form.new' => [
				'content' => $result->getContent(),
				'url' => $this->container->get('router')->generate('creonit_admin_module', ['module' => 'forms'], Router::ABSOLUTE_URL),
				'form' => $form,
				'result' => $result,
			]]);
			$mailing->send($message, $notification_email);
		}

		/*if($result->getEmail()){
			$mailingTemplate = $form->getMailingTemplate();
			$message = $mailing->createMessage(
				$mailingTemplate ? '' : $result->getFormTitle(),
				[$mailingTemplate ? $mailingTemplate->getName() : 'form.success' => [
					'content' => $result->getShortContent(),
					'url' => $this->container->get('router')->generate('creonit_admin_module', ['module' => 'forms'], Router::ABSOLUTE_URL),
					'form' => $form,
					'result' => $result,
				]]
			);
			$mailing->send($message, $result->getEmail());
		}*/

	}

}
