<?php
/**
 * Configure laravel-assetic in this file.
 * File modified by AARYAN ADITYA
 * @package slushie/laravel-assetic
 */

return array(
  /*
   * Groups are named settings containing assets and filters,
   * plus an output file.
   */
  'groups' => array(

      /*
      * First create a group then add scripts into group.. 
      * if you want add folder then mention folderpath/*extension
      * By Default it will take public path.. you can also mention base path.
      * mention output file name  where scripts should compile...
      * finally add group name in views as
      * <script src="<?php echo Asset::url('singlejs-frontend'); ?>"></script>
      */

    //This group associated with javascripts of front-end website

     /*
     @external scrpt call in views :
     <script src="<?php echo Asset::url('singlejs-frontend'); ?>"></script>
     */ 

    'gora-main-js' => array(
      // named filters are defined below
      'filters' => array(
      ),

      'assets' => array(
        'assets/js/jquery-2.1.1.min.js',
        'assets/js/jquery-ui.js',
        'assets/js/bootstrap.js'
      ),

      // output path (probably relative to public)
      // must be rewritable
      'output' => 'gora-main-js'
    ),

     // Adding css to singlecss-main group

    'gora-main-css' => array(
      // you define multiple filters in array
      'filters' => array(
        'css_import',
        'css_import'
      ),

      // named assets defined below
      'assets' => array(
        'assets/css/bootstrap.css',
        'assets/css/jquery-ui.css',
        'assets/css/gora.css',
        'assets/css/gora.min.css',
        'font-awesome-4.1.0/css/font-awesome.min.css'
        ),

      // output path (probably relative to public)
      // must be rewritable
      'output' => 'gora-main-css'
    ),

  ),

  'filters' => array(
    // filter with a closure constructor
    'yui_js' => function() {
      return new Assetic\Filter\Yui\JsCompressorFilter('yui-compressor.jar');
    },

    // filter with a simple class name
    'js_min'      => 'Assetic\Filter\JSMinFilter',
    'css_import'  => 'Assetic\Filter\CssImportFilter',
    'css_min'     => 'Assetic\Filter\CssMinFilter',
    'css_rewrit'  => 'Assetic\Filter\CssRewriteFilter',
    'emed_css'    => 'Assetic\Filter\PhpCssEmbedFilter',
    'coffe_script'=> 'Assetic\Filter\CoffeeScriptFilter',
    'less_php'    => 'Assetic\Filter\LessphpFilter',
  ),

  'assets' => array(
    // name => absolute path to asset file
    // 'jquery' => public_path('script/jquery.js'),
  )
);