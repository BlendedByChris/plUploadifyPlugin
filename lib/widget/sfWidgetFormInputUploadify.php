<?php

sfApplicationConfiguration::getActive()->loadHelpers('Url');

/**
 * sfWidgetFormInputUploadify class
 * 
 * This provides file upload widget for file uploads with the Uploadify
 * javascript library.
 *
 * @package default
 * @author Chris LeBlanc <chris@webPragmatist.com>
 * @see 
 */
class sfWidgetFormInputUploadify extends sfWidgetFormInputFile
{
  /**
   * Instance counter
   *
   * @var integer
   */
  protected static $INSTANCE_COUNT = 0;

  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('uploadify_path', '/plUploadifyPlugin/vendor/jquery-uploadify');
  }

  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * The array keys are files and values are the media names (separated by a ,):
   *
   *   array('/path/to/file.css' => 'all', '/another/file.css' => 'screen,print')
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array(
      $this->getOption('swfupload_css_path') => 'all'
    );
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    $js = array(
      $this->getOption('uploadify_path') . '/swfobject.js',
      $this->getOption('uploadify_path') . '/jquery.uploadify.v2.1.0.min.js'
    );

    if($this->getOption('include_jquery'))
      $js[] = "http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js";
      
    return $js;
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    self::$INSTANCE_COUNT++;

    $output = parent::render($name, $value, $attributes, $errors);

    $widget_id  = $this->getAttribute('id') ? $this->getAttribute('id') : $this->generateId($name);
    $session_name = ini_get('session.name');
    $session_id = session_id();
    $uploader = $this->getOption('uploadify_path') . '/uploadify.swf';
    $cancel_img = $this->getOption('uploadify_path') . '/cancel.png';
    
    $output .= <<<EOF
      <div class="swfupload-buttontarget">
        <noscript>
          We're sorry.  SWFUpload could not load.  You must have JavaScript enabled to enjoy SWFUpload.
        </noscript>
      </div>
      <script type="text/javascript">
        //<![CDATA[
        $(document).ready(function() {
          $('#$widget_id').uploadify({
            'scriptData': {'$session_name':'$session_id', 'media[_csrf_token]':$('#$widget_id').closest('form').find("input[name$='_csrf_token]']").val()},
            'uploader': '$uploader',
            'cancelImg': '$cancel_img',
            'auto'      : true,
            'script': $('#$widget_id').closest('form').attr('action')+'/upload',
            'folder': '/',
            'multi': false,
            'displayData': 'speed',
            'simUploadLimit': 2
          });
        });
        //]]>
      </script>
EOF;
    return $output;
  }
}