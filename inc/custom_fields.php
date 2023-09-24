<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/*CUSTOM FIELDS
 * adds custom fields to the various section
 **/
?>
<div class="wrap">
    <div class="page-header">
        <h1><?php _e( 'WP smart CRM custom fields', 'cpsmartcrm' ); ?><small></small></h1>
    </div>
    <form method="post">
        <div id="tabstrip">
            <ul>
                <li class="k-state-active">Clienti
                    </li>
                <li>a
                    </li>
                <li>b
                    </li>
                <li>c
                    </li>
            </ul>
            <div id="custom_fields_clienti" class="columns">
                <div class="panel panel-default">
                    <div class="panel-heading">

                        <h3 class="panel-title">Add fields to Clienti</h3>
                    </div>
                    <div class="panel-body">
                        <div id="clienti-form-builder" class="col-md-6"></div>

                        <script>
                            jQuery(document).ready(function ($) {
                                $('#clienti-form-builder').formbuilder({
                                	'save_url': 'WPsCRM_save_clients_fields',
                                	'load_url': 'WPsCRM_get_clients_fields',
                                    'useJson': true
                                });
                                $(function () {
                                    $("#clienti-form-builder ul").sortable({ opacity: 0.6, cursor: 'move' });
                                });
                            });
                            </script>
                    </div>
                </div>
            </div>
            <div id="custom_fields_articoli">
                <div class="panel panel-default">
                    <div class="panel-heading">

                        <h3 class="panel-title">Add fields to Articoli</h3>
                    </div>
                    <div class="panel-body">
                        <div id="articoli-form-builder" class="col-md-6"></div>

                        <script>
                            jQuery(document).ready(function ($) {
                                $('#articoli-form-builder').formbuilder({
                                    'save_url': '<?php echo plugin_dir_url( __FILE__ ). 'formbuilder/save.php?section=articoli'?>',
                                    'load_url': '<?php echo plugin_dir_url( __FILE__ ). 'formbuilder/formarticoli.json.php'?>',
                                    'useJson': true
                                });
                                $(function () {
                                    $("#articoli-form-builder ul").sortable({ opacity: 0.6, cursor: 'move' });
                                });
                            });
                            </script>
                    </div>
                </div>
            </div>
            <div id="custom_fields_fatture">
            </div>
            <div>
                <span class="cloudy">&nbsp;</span>
                <div class="weather">
                    <h2>16<span>&ordm;C</span></h2>
                    <p>Cloudy weather in Moscow.</p>
                </div>
            </div>
        </div>
        <script>

            jQuery(document).ready(function ($) {
                $('._saveFields').on('click', function () {
                    console.log($(this).parent())
                    var form = {}
                    form.id = $(this).parent().attr('id');
                    var field = {}, fields = [];
                    $(this).parent().find('ul input').each(function () {

                        field.id = $(this).attr('id');
                        field.type = $(this).attr('type');
                        fields.push(field);
                    })

                    console.log(fields);
                })
                $("#tabstrip").kendoTabStrip({
                    animation: {
                        open: {
                            effects: "fadeIn"
                        }
                    }
                });
            });
        </script>
        <?php
    //submit_button();
        ?>
    </form>
</div>
<!-- /.wrap -->
