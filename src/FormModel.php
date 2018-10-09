<?php

namespace Styde\Html;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Htmlable;
use Styde\Html\FormModel\{FieldCollection, ButtonCollection};

abstract class FormModel implements Htmlable
{
    /**
     * @var \Styde\Html\FormBuilder
     */
    protected $formBuilder;

    /**
     * @var \Styde\Html\Theme
     */
    protected $theme;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \Styde\Html\Form
     */
    public $form;

    /**
     * @var \Styde\Html\FormModel\FieldCollection
     */
    public $fields;

    /**
     * @var \Styde\Html\FormModel\ButtonCollection
     */
    public $buttons;

    public $method = 'post';

    public $customTemplate;

    /**
     * Form Model constructor.
     *
     * @param FormBuilder $formBuilder
     * @param FieldCollection $fields
     * @param ButtonCollection $buttons
     * @param Theme $theme
     */
    public function __construct(FormBuilder $formBuilder, FieldCollection $fields, ButtonCollection $buttons, Theme $theme)
    {
        $this->formBuilder = $formBuilder;
        $this->fields = $fields;
        $this->buttons = $buttons;

        $this->theme = $theme;
    }

    /**
     * Set the form method as post.
     *
     * @return $this
     */
    public function forCreation()
    {
        $this->method = 'post';
        return $this;
    }

    /**
     * Set the form method as put.
     *
     * @return $this
     */
    public function forUpdate()
    {
        $this->method = 'put';
        return $this;
    }

    /**
     * Run the setup.
     *
     * @return void
     */
    protected function runSetup()
    {
        if ($this->form) {
            return;
        }

        $this->form = $this->formBuilder->make($this->method());

        $this->setup($this->form, $this->buttons);
        $this->fields($this->fields);

        switch ($this->method()) {
            case 'post':
                $this->creationSetup($this->form, $this->buttons);
                $this->creationFields($this->fields);
                break;
            case 'put':
                $this->updateSetup($this->form, $this->buttons);
                $this->updateFields($this->fields);
                break;
        }
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Setup the common form attributes and buttons.
     *
     * @param \Styde\Html\Form $form
     * @param \Styde\Html\FormModel\ButtonCollection $buttons
     * @return void
     */
    public function setup(Form $form, ButtonCollection $buttons)
    {
        //...
    }

    /**
     * Setup the form attributes and buttons for creation.
     *
     * @param \Styde\Html\Form $form
     * @param \Styde\Html\FormModel\ButtonCollection $buttons
     * @return void
     */
    public function creationSetup(Form $form, ButtonCollection $buttons)
    {
        //...
    }

    /**
     * Setup the form attributes and buttons for update.
     *
     * @param \Styde\Html\Form $form
     * @param \Styde\Html\FormModel\ButtonCollection $buttons
     * @return void
     */
    public function updateSetup(Form $form, ButtonCollection $buttons)
    {
        //...
    }

    /**
     * Setup the common form fields.
     *
     * @param \Styde\Html\FormModel\FieldCollection $fields
     * @return void
     */
    public function fields(FieldCollection $fields)
    {
        //...
    }

    /**
     * Setup the form fields for creation.
     *
     * @param \Styde\Html\FormModel\FieldCollection $fields
     * @return void
     */
    public function creationFields(FieldCollection $fields)
    {
        //...
    }

    /**
     * Setup the form fields for update.
     *
     * @param \Styde\Html\FormModel\FieldCollection $fields
     * @return void
     */
    public function updateFields(FieldCollection $fields)
    {
        //...
    }

    /**
     * Set a new custom template
     *
     * @param string $template
     * @return $this
     */
    public function template($template)
    {
        $this->customTemplate = $template;

        return $this;
    }

    /**
     * Set a new Model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return $this
     */
    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Render all form to Html
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->render();
    }

    /**
     * @param string|null $customTemplate
     * @return string
     */
    public function render($customTemplate = null)
    {
        $this->runSetup();

        return $this->theme->render($customTemplate ?: $this->customTemplate, [
            'form' => $this->form,
            'fields' => $this->fields,
            'buttons' => $this->buttons,
        ], 'form');
    }

    /**
     * Validate the request with the validation rules specified
     *
     * @param Request|null $request
     * @return mixed
     */
    public function validate(Request $request = null)
    {
        return ($request ?: request())->validate($this->getValidationRules());
    }

    /**
     * Get all rules of validation
     *
     * @return array
     */
    public function getValidationRules()
    {
        $this->runSetup();

        $rules = [];

        foreach ($this->fields->all() as $name => $field) {
            if ($field->included) {
                $rules[$name] = $field->getValidationRules();
            }
        }

        return $rules;
    }
}
