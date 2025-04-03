<?php

namespace App\Lib;

use App\Models\Form;
use App\Rules\FileTypeValidate;

class FormProcessor {
    public function generatorValidation() {
        $validation['rules'] = [
            'form_generator.is_required.*' => 'required|in:required,optional',
            'form_generator.extensions.*' => 'nullable',
            'form_generator.options.*' => 'nullable',
            'form_generator.form_label.*' => 'required',
            'form_generator.form_type.*' => 'required|in:text,select,radio,textarea,checkbox,file,email,url,number,datetime,date,time',
            'form_generator.form_width.*' => 'required|in:12,6,4,3',
        ];
        $validation['messages'] = [
            'form_generator.is_required.*.required' => 'All is required field is required',
            'form_generator.is_required.*.in' => 'Is required field is invalid',
            'form_generator.form_label.*.required' => 'All form label is required',
            'form_generator.form_type.*.required' => 'All form type is required',
            'form_generator.form_type.*.in' => 'Some selected form type is invalid',
            'form_generator.form_width.*.required' => 'All form width is required',
            'form_generator.form_width.*.in' => 'Some selected form width is invalid',
        ];
        return $validation;
    }

    public function generate($act, $isUpdate = false, $identifierField = 'act', $identifier = null) {
        $forms = request()->form_generator;
        $formData = [];
        if ($forms) {
            for ($i = 0; $i < count($forms['form_label']); $i++) {
                $extensions = $forms['extensions'][$i];
                if ($extensions != 'null' && $extensions != null) {
                    $extensionsArr = explode(',', $extensions);
                    $notMatchedExt = count(array_diff($extensionsArr, $this->supportedExt()));
                    if ($notMatchedExt > 0) {
                        throw new \Exception("Your selected extensions are invalid");
                    }
                }
                $label = titleToKey($forms['form_label'][$i]);
                $formData[$label] = [
                    'name' => $forms['form_label'][$i],
                    'label' => $label,
                    'is_required' => $forms['is_required'][$i],
                    'instruction' => @$forms['instruction'][$i],
                    'extensions' => $forms['extensions'][$i] == 'null' ? "" : $forms['extensions'][$i],
                    'options' => $forms['options'][$i] ? explode(",", $forms['options'][$i]) : [],
                    'type' => $forms['form_type'][$i],
                    'width' => $forms['form_width'][$i],
                ];
            }
        }
        if ($isUpdate) {
            if ($identifierField == 'act') {
                $identifier = $act;
            }
            $form = Form::where($identifierField, $identifier)->first();
            if (!$form) {
                $form = new Form();
            }
        } else {
            $form = new Form();
        }
        $form->act = $act;
        $form->form_data = $formData;
        $form->save();
        return $form;
    }

    public function valueValidation($formData) {
        $validationRule = [];
        $rule = [];

        foreach ($formData as $data) {
            if ($data->is_required == 'required') {
                $rule = array_merge($rule, ['required']);
            } else {
                $rule = array_merge($rule, ['nullable']);
            }
            if ($data->type == 'select' || $data->type == 'checkbox' || $data->type == 'radio') {
                $rule = array_merge($rule, ['in:' . implode(',', $data->options)]);
            }
            if ($data->type == 'file') {
                $rule = array_merge($rule, [new FileTypeValidate(explode(',', $data->extensions))]);
            }
            if ($data->type == 'email') {
                $rule = array_merge($rule, ['email']);
            }
            if ($data->type == 'url') {
                $rule = array_merge($rule, ['url']);
            }
            if ($data->type == 'number') {
                $rule = array_merge($rule, ['integer']);
            }
            if ($data->type == 'checkbox') {
                $rule = array_merge($rule, ['array']);
                $validationRule[$data->label] = $rule;
            } else {
                $validationRule[$data->label] = $rule;
            }
            $rule = [];
        }
        return $validationRule;
    }

    public function processFormData($request, $formData) {
        $requestForm = [];
        foreach ($formData as $data) {
            $name = $data->label;
            $value = $request->$name;
            if ($data->type == 'file') {
                if ($request->hasFile($name)) {
                    $directory = date("Y") . "/" . date("m") . "/" . date("d");
                    $path = getFilePath('verify') . '/' . $directory;
                    $value = $directory . '/' . fileUploader($value, $path);
                } else {
                    $value = null;
                }
            }
            $requestForm[] = [
                'name' => $data->name,
                'type' => $data->type,
                'value' => $value,
            ];
        }
        return $requestForm;
    }

    public function supportedExt() {
        return [
            'jpg',
            'jpeg',
            'png',
            'pdf',
            'doc',
            'docx',
            'txt',
            'xlx',
            'xlsx',
            'csv',
            'avi',
            'flv',
            'gif',
            'm4a',
            'mp3',
            'mp4',
            'ppsx',
            'rtf',
            'wav',
            'xla',
        ];
    }
}
