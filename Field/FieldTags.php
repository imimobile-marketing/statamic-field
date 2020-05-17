<?php

namespace Statamic\Addons\Field;

use Exception;
use Request as Req;
use Statamic\API\Form;
use Statamic\Extend\Tags;

class FieldTags extends Tags
{
    /**
     * The {{ field }} tag
     *
     * @return string|array
     */
    public function index()
    {
      dd($this->getFieldData());
    }

    /**
     * The {{ field:old }} tag
     *
     * @return string|array
     */
    public function old()
    {
      $field = $this->getParam('src');

      $fields = $this->getFieldData();

      $fieldData = $fields[$field];

      if (!empty($fieldData['old'])) {
        return $fieldData['old'];
      }
      return null;
    }

    /**
     * The {{ field:error }} tag
     *
     * @return string|array
     */
    public function error()
    {
      $this->formsetName = $formset = $this->getFormset();

      $this->errorBag = $this->getErrorBag();

      $field = $this->getParam('src');
      
      if ($this->hasErrors()) {
        return $this->getErrors()[$field];
      }

      return null;
    }

    
    public function hasError()
    {
      $this->formsetName = $formset = $this->getFormset();
      $this->errorBag = $this->getErrorBag();

      $errors = collect([]);
      if ($this->hasErrors()) {
        $errors = collect($this->errorBag->keys());
      }

      $src = $this->getParam('src');

      if ($errors->contains($src)) {
        return true;
      }
      return false;
    }

    /**
     * Get the formset specified either by the parameter or from within the context
     *
     * @return string
     */
    protected function getFormset()
    {
      if (! $formset = $this->get(['formset', 'in'], array_get($this->context, 'formset'))) {
        throw new \Exception('A formset is required on Form tags. Please refer to the docs for more information.');
      }

      return $formset;
    }

    protected function getFieldData()
    {
      $fields = [];

      $this->formsetName = $formset = $this->getFormset();
      $this->errorBag = $this->getErrorBag();

      $errors = collect([]);
      if ($this->hasErrors()) {
        $errors = collect($this->errorBag->keys());
      }

      $form = collect(Form::fields($formset));
      
      foreach ($form as $key => $data) {

        $fields[$data['name']] = [
          'field' => $data['name'],
          'name' => $data['name'],
          'old' => $data['old'],
          'display' => $data['display'],
          'validate' => $data['validate'],
        ];

        if ($errors->contains($data['name'])) {
          $fields[$data['name']] += [
            'error' => $this->getErrors()[$data['name']]
          ];
        }
      }

      return $fields;
    }

     /**
     * Get the errorBag from session
     *
     * @return object
     */
    protected function getErrorBag()
    {
      if ($this->hasErrors()) {
        return session('errors')->getBag('form.'.$this->formsetName);
      }
    }

    /**
     * Does this form have errors?
     *
     * @return bool
     */
    protected function hasErrors()
    {
      if (! $formset = $this->getFormset()) {
        return false;
      }

      return (session()->has('errors'))
            ? session()->get('errors')->hasBag('form.'.$formset)
            : false;
    }

    /**
     * Get an array of all the error messages, keyed by their input names
     *
     * @return array
     */
    protected function getErrors()
    {
      return array_combine($this->errorBag->keys(), $this->getErrorMessages());
    }

    /**
     * Get an array of all the error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
      return $this->errorBag->all();
    }
}
