<?php namespace Smallneat\BootstrapForms;

use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class BootstrapFormsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;



    /**
     * Called to boot the package
     */
    public function boot()
    {
        // register the package
        $this->package('smallneat/bootstrap-forms');

        // Find the form object
        $form = $this->app['form'];

        // Start to define all the extra form macros
        $form->macro('textField', function($name, $label = null, $value = null, $attributes = array()) use ($form)
        {
            // Get the plain form element and wrap it
            $element = $form->text($name, $value, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });



        // Date Field
        $form->macro('dateField', function($name, $label = null, $value = null, $attributes = array()) use ($form)
        {
            $element = $form->input('date', $name, $value, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });

        // Email Field
        $form->macro('emailField', function($name, $label = null, $value = null, $attributes = array()) use ($form)
        {
            $element = $form->email($name, $value, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });


        // Password Field
        $form->macro('passwordField', function($name, $label = null, $attributes = array()) use ($form)
        {
            $element = $form->password($name, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });


        // Text Area
        $form->macro('textareaField', function($name, $label = null, $value = null, $attributes = array()) use ($form)
        {
            $element = $form->textarea($name, $value, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });


        // Select Field
        $form->macro('selectField', function($name, $label = null, $options, $value = null, $attributes = array()) use ($form)
        {
            $element = $form->select($name, $options, $value, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });


        // Select Field (Multiple)
        $form->macro('selectMultipleField', function($name, $label = null, $options, $value = null, $attributes = array()) use ($form)
        {
            $attributes = array_merge($attributes, ['multiple' => true]);
            $element = $form->select($name, $options, $value, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });

        $form->macro('fileField', function($name, $label = null, $attributes = array()) use ($form)
        {
            $element = $form->file($name, array_merge(['class' => 'form-control', 'id' => 'id-field-' . str_replace('[]', '', $name), 'help'=>''], $attributes));
            return $form->fieldWrapper($name, $label, $element, $attributes);
        });




        // Radio Buttons are different
        $form->macro('radioField', function($name, $label = null, $value = 1, $checked = null, $attributes = array()) use ($form)
        {
            // Get a version of the name that will be good for properties
            $simpleName = str_replace('[]', '', $name);

            $attributes = array_merge(['id' => 'id-field-' . $simpleName], $attributes);
            $out = '<div class="radio">';
            $out .= '<label>';
            $out .= $form->radio($name, $value, $checked, $attributes) . ' ';
            $out .= $label;
            $out .= '</label>';
            $out .= '</div>';
            return $out;
        });


        // Checkbox's are different again
        $form->macro('checkboxField', function($name, $label = null, $value = 1, $checked = null, $attributes = array()) use ($form)
        {
            $errors = Session::get('errors');
            $errorMsg = null;
            if ($errors && ($errors->has($name))) {
                $errorMsg = $errors->first($name);
            }

            // Get a version of the name that will be good for properties
            $simpleName = str_replace('[]', '', $name);

            // Generate some HTML
            $out = '<div class="form-group';
            $out .= $errorMsg ? ' has-error checkbox-has-error has-feedback' : '';
            $out .= '">';

            $out .= '<label>';
            $out .= $form->checkbox($name, $value, $checked, array_merge(['id' => 'id-field-' . $simpleName], $attributes)) . ' ';
            $out .= $label;
            $out .= '</label>';

            // Add error messages
            if ($errorMsg) {
                $out .= '<span class="help-block">'.$errorMsg.'</span>';
            }

            $out .= '</div>';
            return $out;
        });



        // This is the macro that does the wrapping for most common field types
        $form->macro('fieldWrapper', function($name, $label, $element, $attributes)
        {
            $errors = Session::get('errors');
            $errorMsg = null;
            if ($errors && ($errors->has($name))) {
                $errorMsg = $errors->first($name);
            }

            // Get a version of the name that will be good for properties
            $simpleName = str_replace('[]', '', $name);

            // Generate some HTML
            $out = '<div class="form-group';
            $out .= $errorMsg ? ' has-error has-feedback' : '';
            $out .= '">';

            // Add a label if there is one
            if (!is_null($label)) {
                $out .= '<label for="id-field-' . $simpleName . '" class="control-label">';
                $out .= $label;

                // Add a * to the label for required fields
                if (in_array('required', $attributes)) {
                    $out .= '  <span class="text-danger" title="Required">*</span>';
                }

                $out .= '</label>';
            }

            // Add the field
            $out .= $element;

            // Add error messages
            if ($errorMsg) {
                //$out .= '<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
                $out .= '<span class="help-block">'.$errorMsg.'</span>';
            }

            // Add in help text...
            if (array_key_exists('help', $attributes)) {
                $out .= '<span class="help-block">'.$attributes['help'].'</span>';
            }

            // Finish
            $out .= '</div>';

            return $out;
        });
    }


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
