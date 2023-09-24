<?php 
$ID=$_REQUEST["ID"];
$table=WPsCRM_TABLE."clienti";

$ID_azienda="1";
$where="FK_aziende=$ID_azienda";
if ($ID)
{
	$sql="select * from $table where ID_clienti=$ID";
    $riga=$wpdb->get_row($sql, ARRAY_A);
	$agente=$riga["agente"];
}
?>
<script type="text/javascript">
jQuery(document).ready(function ($) {
  $(".btn_todo").click(function () 
  { 
    $('#dialog_todo').show();
  });
  $(".btn_appuntamento").click(function () 
  { 
    $('#dialog_appuntamento').show();
  });
        $("#grid").kendoGrid({
        dataSource: {
          transport: {
            read: function (options) {
              $.ajax({
                url: ajaxurl,
                data: {
                  'action': 'get_client_scheduler',
                  'id_cliente': '<?php echo $ID?>'
                },
                success: function (result) {
                  //console.log(result);
                  $("#grid").data("kendoGrid").dataSource.data(result.scheduler);
                
                },
                error: function (errorThrown) {
                  console.log(errorThrown);
                }
              })
            }
          },
          schema: {
            model: {
              id: "id_agenda",
              fields: {
                tipo: { editable: false },
                oggetto: { editable: false },
                annotazioni: { editable: false },
                data_scadenza: { type:"date", editable: false },
              }
            }
          }
        },
        columns: [{ field: "id_agenda", title:"ID" }, { field: "tipo", title:"Tipo" }, { field: "oggetto", title:"Oggetto" }, { field: "annotazioni", title:"Descrizione" }, { field: "data_scadenza", title:"Scadenza", format: "{0: dd-MM-yyyy}" },
        { command: [
          {
            name: "Apri",
            click: function(e) {
              var tr = $(e.target).closest("tr"); // get the current table row (tr)
              var data = this.dataItem(tr);
              location.href="?page=smart-crm&p=scheduler/view.php&ID="+data.id;
            }
          },
          { name: "destroy" } // built-in "destroy" command
        ]
        }
        ],
        height: 500,
        editable:"popup"
      });
      
      var _contacts = new kendo.data.DataSource({
	    type: "json",
	    transport: {
	                read: function (options) {
	                    $.ajax({
	                        url: ajaxurl,
	                        data: {
	                            'action': 'get_client_contacts',
	                            'client_id': '<?php echo $ID?>'
	                        },
	                        success: function (result) {
	                            //console.log(result);
	                            $("#grid_contacts").data("kendoGrid").dataSource.data(result.contacts);

	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
	                },
	                create: function (options) {
                      //console.log("Update", options);
                      options.success(options.data);
                      $.ajax({
	                        url: ajaxurl,
	                        data: {
	                            'action': 'save_client_contact',
	                            'client_id': '<?php echo $ID?>',
	                            'row': options.data
	                        },
	                        success: function (result) {
	                            //console.log(result);
	                            $("#grid_contacts").data("kendoGrid").dataSource.data(result.clients);

	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
	                },
	                update: function (options) {
                        //console.log("Update", options);
                        options.success(options.data);
                        $.ajax({
	                        url: ajaxurl,
	                        data: {
	                            'action': 'save_client_contact',
	                            'client_id': '<?php echo $ID?>',
	                            'row': options.data
	                        },
	                        success: function (result) {
	                            //console.log(result);
	                            $("#grid_contacts").data("kendoGrid").dataSource.data(result.clients);

	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
                    },
                    destroy: function (options) {
                        console.log("Delete", options);
                        options.success(options.data);
                        $.ajax({
	                        url: ajaxurl,
	                        data: {
	                            'action': 'delete_client_contact',
	                            'client_id': '<?php echo $ID?>',
	                            'row': options.data
	                        },
	                        success: function (result) {
	                            //console.log(result);
	                            $("#grid_contacts").data("kendoGrid").dataSource.data(result.clients);

	                        },
	                        error: function (errorThrown) {
	                            console.log(errorThrown);
	                        }
	                    })
                    },
                  parameterMap: function(options, operation) {
                      if (operation !== "read" && options.models) {
                          return {models: kendo.stringify(options.models)};
                      }
                      return kendo.stringify(options);
                  }
	            },
	    schema: {
	        //data: "data",
	        model: {
	            id: "id",
	            fields: {
	                id: { editable: false },
	                nome: { editable: true },
	                cognome: { editable: true },
	                email: { editable: true },
	                telefono: { editable: true },
	                qualifica: { editable: true },
	                }
	            }
	        },
	    pageSize: 50,
	    //total:data.length()
	    });
            $("#grid_contacts").kendoGrid({
                dataSource: _contacts,
                height: 550,
                groupable: true,
                sortable: true,
                serverPaging: true,
                pageable:
                    {
                        pageSizes: [20, 50, 100],
                        messages:
                            {
                                display: "<?php _e('Showing','manyposts') ?> {0}-{1}  <?php _e('of','manyposts') ?> {2} <?php _e('total','manyposts') ?>",
                                of: "<?php _e('of','manyposts') ?> {0}",
                                itemsPerPage: "<?php _e('Posts per page','manyposts') ?>",
                                first: "<?php _e('First page','manyposts') ?>",
                                last: "<?php _e('Last page','manyposts') ?>",
                                next: "<?php _e('Next','manyposts') ?>",
                                previous: "<?php _e('Prev.','manyposts') ?>",
                                refresh: "<?php _e('Reload','manyposts') ?>",
                                morePages: "<?php _e('More','manyposts') ?>"
                            },
                    },
                filterable:
    {
        messages:
            {
                info: "<?php _e('Filter by','cpsmartcrm') ?> "
            },
        extra: false,
        operators:
            {
                string:
                    {
                        contains: "<?php _e('Contains','cpsmartcrm') ?> ",
                        startswith: "<?php _e('Starts with','cpsmartcrm') ?>",
                        eq: "<?php _e('Equal','cpsmartcrm') ?>",
                        neq: "<?php _e('Not equal','cpsmartcrm') ?>"
                    }
            }
    },
    toolbar: ["create"],
    columns: [
      { 
        field: "id", 
        title: "<?php _e('ID','cpsmartcrm') ?>"
      }, 
      { 
        field: "nome", 
        title: "<?php _e('Nome','cpsmartcrm') ?>"
      }, 
      { 
        field: "cognome", 
        title: "<?php _e('Cognome','cpsmartcrm') ?>"
      }, 
      { 
        field: "email", 
        title: "<?php _e('Email','cpsmartcrm') ?>"
      }, 
      { 
        field: "telefono", 
        title: "<?php _e('Telefono','cpsmartcrm') ?>"
      },
      { 
        field: "qualifica", 
        title: "<?php _e('Qualifica','cpsmartcrm') ?>"
      },
        { command: ["edit", "destroy"], title: "&nbsp;", width: "250px" }],
                editable: "inline"
            });
});
  
  
	function annulla()
	{
		location.href="?page=smart-crm&p=clienti/list.php";
	}
	function elimina()
	{
		if (!confirm("Eliminare il cliente selezionato?\nL'eliminazione sara' solo logica, e sar&agrave; possibile ripristinare il cliente"))
			return;
		location.href="?page=smart-crm&p=clienti/elimina.php&ID=<?php echo $ID?>";
	}
	function check_form()
	{
		var form=document.forms["form_insert"];
		form.submit();
	}

</script>
<form name="form_insert" action="?page=smart-crm&p=clienti/insert.php&ID=<?php echo $ID?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
    <h3><?php if ($ID) { ?> <?php _e('Cliente','cpsmartcrm')?>: <?php echo $riga["nome"]?> <?php echo $riga["cognome"]; } else{ ?> <?php _e('New Client','cpsmartcrm')?> <?php } ?></h3>
    <ul class="select-action">
        <li class="btn btn-success btn-sm _flat"><i class="glyphicon glyphicon-floppy-disk"></i> 
            <b onClick="check_form();return false;"> <?php _e('Save','cpsmartcrm')?></b>
        </li>
        <li class="btn btn-warning btn-sm _flat"><i class="glyphicon glyphicon-floppy-remove"></i>
            <b onClick="annulla();return false;"> <?php _e('Reset','cpsmartcrm')?></b>
        </li>
        <?php if ($ID){?>
        <li class="btn btn-danger btn-sm _flat"><i class="glyphicon glyphicon-remove"></i> 
            <b onClick="elimina();return false;"> <?php _e('Delete','cpsmartcrm')?></b>
        </li>

        <li class="btn btn-info btn-sm _flat btn_todo" style="margin-left:30px"><i class="glyphicon glyphicon-tag"></i> 
            <b> <?php _e('NEW TODO','cpsmartcrm')?></b>
        </li>
        <li class="btn btn-default btn-sm _flat btn_appuntamento"><i class="glyphicon glyphicon-pushpin"></i> 
            <b> <?php _e('NEW APPOINTMENT','cpsmartcrm')?></b>
        </li>
        <li class="btn btn-primary btn-sm _flat btn_activity"><i class="glyphicon glyphicon-option-horizontal"></i> 
            <b onClick="return false;"> <?php _e('NEW ACTIVITY','cpsmartcrm')?></b>
        </li>
        <?php } ?>
    </ul>
<div id="tabstrip" style="margin-top:14px">
    <ul>
        <li id="tab1"><?php _e('Master Data','cpsmartcrm')?></li>
        <li><?php _e('Contacts','cpsmartcrm')?></li>
        <li><?php _e('Notes','cpsmartcrm')?></li>
        <li><?php _e('Summary','cpsmartcrm')?></li>
    </ul>
    <!-- TAB 1 -->
<div>
<div id="d_anagrafica">
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Business Name','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="ragione_sociale" maxlength='250' value="<?php echo $riga["ragione_sociale"]?>" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('Date','cpsmartcrm')?></label>
	<div class="col-sm-2"><input type="text" name="data_inserimento" maxlength='10' value="<?php echo inverti_data($riga["data_inserimento"])?>" class="_date">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Codice fiscale','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="cod_fis" value="<?php echo $riga["cod_fis"]?>" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('VAT number','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="p_iva" value="<?php echo $riga["p_iva"]?>" class="form-control">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('First Name','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="nome" value="<?php echo $riga["nome"]?>" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('Last Name','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="cognome" value="<?php echo $riga["cognome"]?>" class="form-control">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Address','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="indirizzo" size="50" maxlength='50' value="<?php echo $riga["indirizzo"]?>" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('ZIP Code','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="cap" size="5" maxlength='5' value="<?php echo $riga["cap"]?>" class="form-control">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Town','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="localita" size="50" maxlength='55' value="<?php echo $riga["localita"]?>" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('State/prov.','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="provincia" size="5" maxlength='5' value="<?php echo $riga["provincia"]?>" class="form-control">
	</div>
</div>

<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Phone','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="telefono1" size="20" maxlength='50' value="<?php echo $riga["telefono1"]?>" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('Phone 2','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="telefono2" size="20" maxlength='50' value="<?php echo $riga["telefono2"]?>" class="form-control">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Fax','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="fax" size="20" maxlength='50' value="<?php echo $riga["fax"]?>" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('Email','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="email" size="20" maxlength='50' value="<?php echo $riga["email"]?>" class="form-control">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Category','cpsmartcrm')?></label>
	<div class="col-sm-4"><? 
	  $catOptions=get_option('CRM_clients_settings');
	  $arr_categories=maybe_unserialize($catOptions['clientsCategories']);

	  ?>
	<select name="categoria" class="form-control">
		<option value="0"></option>
		<?
		foreach($arr_categories as $cat)
		{
		?>
	 	<option value="<?php echo $cat?>" <?php echo $cat==$riga["categoria"]?"selected":""?>><?php echo $cat?></option>
		<?
		}
		?>
	</select>
	</div>
	<label class="col-sm-1 control-label"><?php _e('Web Site','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="sitoweb" size="20" maxlength='50' value="<?php echo $riga["sitoweb"]?>" class="form-control">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Skype','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="skype" size="20" maxlength='100' value="<?php echo $riga["skype"]?>" class="form-control">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Agent','cpsmartcrm')?>:</label>
	<div class="col-sm-4"><select id="selectAgent" name="selectAgent" />
	</div>
	<label class="col-sm-1 control-label"><?php _e('Comes from','cpsmartcrm')?>:</label>
	<div class="col-sm-2"><?php echo provenienza($riga["provenienza"])?>
	</div>
</div>
<?
if (!$riga["user_id"])
{
?>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Create User','cpsmartcrm')?>?</label>
	<div class="col-sm-1"><input type="checkbox" name="crea_utente" value="1">
	</div>
</div>
<div class="row form-group">
	<label class="col-sm-1 control-label"><?php _e('Username','cpsmartcrm')?></label>
	<div class="col-sm-4"><input type="text" name="username" size="20" maxlength='50' value="" class="form-control">
	</div>
	<label class="col-sm-1 control-label"><?php _e('Password','cpsmartcrm')?></label>
	<div class="col-sm-2"><input type="text" name="password" size="20" maxlength='50' value="" class="form-control">
	</div>
</div>
<?}?>
</div>
</div>
    <!--END TAB 1 -->
    <!-- TAB 2 -->
<div>
<?if ($ID){?>
<div id="grid_contacts"></div>
<?}?>
</div>
    <!--END TAB 2 -->
    <!--TAB 4 -->
<div>
<table>
<tr>
	<td align="center"><?php _e('Notes','cpsmartcrm')?></td>
</tr>
<tr>
	<td><textarea name="annotazioni" rows="10" cols="100"><?php echo stripslashes($riga["annotazioni"])?></textarea></td>
</tr>

</table>
</div>
    <!-- END TAB 4 -->
    <!-- TAB 5 -->
<div>
<div id="grid"></div> 
</div>
    <!-- END TAB 5 -->

</div>
 	<br>

</form>
    <ul class="select-action">
        <li class="btn btn-success btn-sm _flat"><i class="glyphicon glyphicon-floppy-disk"></i> 
            <b onClick="check_form();return false;"> <?php _e('Save','cpsmartcrm')?></b>
        </li>
        <li class="btn btn-warning btn-sm _flat"><i class="glyphicon glyphicon-floppy-remove"></i>
            <b onClick="annulla();return false;"> <?php _e('Reset','cpsmartcrm')?></b>
        </li>
        <?php if ($ID){?>
        <li class="btn btn-danger btn-sm _flat"><i class="glyphicon glyphicon-remove"></i> 
            <b onClick="elimina();return false;"> <?php _e('Delete','cpsmartcrm')?></b>
        </li>

        <li class="btn btn-info btn-sm _flat btn_todo" style="margin-left:30px"><i class="glyphicon glyphicon-tag"></i> 
            <b> <?php _e('NEW TODO','cpsmartcrm')?></b>
        </li>
        <li class="btn btn-default btn-sm _flat btn_appuntamento"><i class="glyphicon glyphicon-pushpin"></i> 
            <b> <?php _e('NEW APPOINTMENT','cpsmartcrm')?></b>
        </li>
        <li class="btn btn-primary btn-sm _flat btn_activity"><i class="glyphicon glyphicon-option-horizontal"></i> 
            <b onClick="return false;"> <?php _e('NEW ACTIVITY','cpsmartcrm')?></b>
        </li>
        <?php } ?>
    </ul>
<div id="dialog_todo" style="display:none;margin: 0 auto; text-align: center; z-index: 1000; width: 100%; height: 100%; background: url('<?php echo str_replace("inc/crm/clienti/","",plugin_dir_url( __FILE__ ))?>css/img/bg_w_tr.png');position: absolute;left: 0;top:0;"  class=" _modal">
    
        <?php
        include (__DIR__.'/form_todo.php')?>

</div>
<div id="dialog_appuntamento" style="display:none;margin: 0 auto; text-align: center; z-index: 1000; width: 100%; height: 100%; background: url('<?php echo str_replace("inc/crm/clienti/","",plugin_dir_url( __FILE__ ))?>css/img/bg_w_tr.png');position: absolute;left: 0;top:0;"  class=" _modal">

   <?php
      include (__DIR__.'/form_appuntamento.php')?>

</div>

<script>
    var media_uploader = null;

    function open_media_uploader_multiple_images() {
        media_uploader = wp.media({
            frame: "post",
            state: "insert",
            multiple: true
        });

        media_uploader.on("insert", function () {

            var length = media_uploader.state().get("selection").length;
            var images = media_uploader.state().get("selection").models
            console.log(images);

            for (var iii = 0; iii < length; iii++) {
                var image_url = images[iii].changed.url;
                console.log(image_url);
                jQuery('.thumbContainer').append('<img src="' + image_url.replace(".jpg", "-150x150.jpg") + '">')
                var image_caption = images[iii].changed.caption;
                var image_title = images[iii].changed.title;
            }
        });

        media_uploader.open();
    }
    jQuery(document).ready(function ($) {
        $.ajax({
            //url: "<?php //echo plugin_dir_url(  dirname(dirname(__FILE__)) ). '/formbuilder/formclienti.json.php'?>",
            url:ajaxurl,
            data: {
                'action': 'get_clients_fields_values',
                'id': '<?php echo $ID?>'
            }
        }).done(function (response) {
            var obj = response
            obj = obj.form_structure;
            parseFields(obj);
         
            //colorpicker
            $('._colorPicker').kendoColorPicker({
                value: "#ffffff",
                buttons: false,
                select: onColorSelect
            });

            function onColorSelect(e) {
                console.log(e.value);
            }
            //dateTime
            $("._dateTime").kendoDateTimePicker({
                value: new Date(),
                change:onDateSelect()
            });
            function onDateSelect() {
                // console.log(kendo.toString(this.value(), 'g'));
            }
            //$('.add_pics').on('click', function () {
            //    open_media_uploader_multiple_images();
            //})
        });
        
        $("#tabstrip").kendoTabStrip({
            animation: {
                // fade-out current tab over 1000 milliseconds
                close: {
                    duration: 500,
                    effects: "fadeOut"
                },
                // fade-in new tab over 500 milliseconds
                open: {
                    duration: 500,
                    effects: "fadeIn"
                }
            }
        });
        var tabToActivate = $("#tab1");
        $("#tabstrip").kendoTabStrip().data("kendoTabStrip").activateTab(tabToActivate);
        $("._date").kendoDatePicker({
            value: new Date(),format: 'dd-MM-yyyy'
        });
        function parseFields(fields) {
            console.log(fields)
            var _html = "";

            for (i = 0; i < fields.length; i++) {
                var _required="";
              //  console.log(fields[i])
                if (fields[i].required == "checked")
                    _required = 'required';
                if (fields[i].cssClass == "color") {
                    _html += ('<tr class="extra_field"><td colspan="4"><label>' + fields[i].values + '</label><input class="_colorPicker" id="clienti_' + i + '_color" name="clienti_' + i + '_color" ' + _required + '/></td></tr>\n');
                }
                if (fields[i].cssClass == "datetime") {
                    _html += ('<tr class="extra_field"><td colspan="4"><label>' + fields[i].values + '</label><input class="_dateTime" id="clienti_' + i + '_datetime" name="clienti_' + i + '_datetime"  ' + _required + '/></td></tr>\n');
                }
                if (fields[i].cssClass == "input_text") {
                    //_html += ('<div class="row form-group"><label class="col-sm-1 control-label">' + fields[i].values + '</label><div class="col-sm-4"><input class="form-control" type="text" style="width:250px" id="' + fields[i].field_name + '" name="' + fields[i].field_name + '" value="' + fields[i].valore + '"  ' + _required + '/></div></div>\n');
                    _html += ('<tr class="extra_field"><td colspan="4"><label>' + fields[i].values + '</label><input class="form-control" type="text" style="width:250px" id="' + fields[i].field_name + '" name="' + fields[i].field_name + '" value="' + fields[i].valore + '"  ' + _required + '/></td></tr>\n');
                }
                if (fields[i].cssClass == "select") {
                    _html += ('<tr class="extra_field"><td colspan="4"><label>' + fields[i].title + '</label><select class="form-control" style="width:250px" id="clienti_' + i + '_select" name="clienti_' + i + '_select"  ' + _required + '>\n');
                    //var $options = JSON.parse(fields[i].values);
                    //var $options = JSON.stringify(fields[i].values);
                    var $options = fields[i].values;
                    //console.log($options)
                    Array.prototype.forEach.call($options, function (item) {
                        //console.log(item);
                    });
                    //$options.each(function (key, value) { console.log(key + " " + value) });


                    for (k = 0; k < $options.length; k++) {
                        //console.log("option: " + $options[k].value)
                        _html += $options[k].value;
                        //_html += ('<option value="' + $options[k].value + '">' + $options[k].value + '</option>');
                    }
                    _html += ('</select></td></tr>\n');
                }
                if (fields[i].cssClass == "checkbox") {
                    _html += ('<tr class="extra_field"><td colspan="4"><label>' + fields[i].title + '</label>');

                    //var $options = JSON.parse(fields[i].values);

                    var $options = fields[i].values;
                    //console.log($options);
                    for (var prop in $options) {
                        //console.log(prop);
                    // for (j = 0; j < $options.length; j++) {
                      // alert($options[j].values);
                      //alert ($options[prop].value);
                      if ($options[prop].value==fields[i].valore)
                        _html += ('&nbsp;<label>' + $options[prop].value + '</label>&nbsp;<input type="checkbox" name="'+fields[i].field_name+'" value="' + $options[prop].value + '"  ' + _required + ' checked />');
                      else
                        _html += ('&nbsp;<label>' + $options[prop].value + '</label>&nbsp;<input type="checkbox" name="'+fields[i].field_name+'" value="' + $options[prop].value + '"  ' + _required + '/>');
                        //console.log("fields: " + prop)
                    }

                    for (k = 0; k < $options.length; k++) {
                        //_html += ('<option value="' + fields[i].values[k].value + '">' + fields[i].values[k].value + '</option>');
                    }
                    _html += ('</select></td></tr>\n');
                }
                if (fields[i].cssClass == "gallery") {
                    _html += ('<tr class="extra_field _gallery"><td ><label>' + fields[i].values + '</label><span class="add_gallery button button-primary" value="Add pics" onClick="open_media_uploader_multiple_images()">Add Pics</span></td>');

	           
                    _html += ('<td colspan="3" class="thumbContainer">Gallery container</td></tr>\n');
                }
                if (fields[i].cssClass == "video") {
                    _html += ('<tr class="extra_field _gallery"><td ><label>' + fields[i].values + '</label><object style="width:100%;height:100%;width: 400px; height: 300px; float: none; clear: both; margin: 2px auto;" data="https://www.youtube.com/embed/jUR7EVoY060">\n\
</object></td>');

                }
            }
            $('#d_anagrafica').after('\n\ <h3>Campi aggiuntivi</h3><table>' + _html+'</table>');

        }
        var _users = new kendo.data.DataSource({
            type: "json",
            transport: {
                read: function (options) {
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            'action': 'get_CRM_users',
                            'role': 'CRM_agent',
                            'include_admin':true
                        },
                        success: function (result) {
                            //console.log(result);
                            $("#selectAgent").data("kendoDropDownList").dataSource.data(result);

                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    })
                }
            }
        });
        $('#selectAgent').kendoDropDownList({
            placeholder: "Select User...",
            dataTextField: "display_name",
            dataValueField: "ID",
            autoBind: true,
            dataSource: _users,     
        });
        if (agente='<?php echo $agente?>')
           $("#selectAgent").data('kendoDropDownList').value(agente);
});
    
</script>
<style>

    .form-control:not(._m){max-width:50%;height:20px}
</style>
