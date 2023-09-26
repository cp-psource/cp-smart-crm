<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<script type="text/javascript">

jQuery(document).ready(function ($) {
    $("#btn_cerca").click(function () 
    {
        var descrizione=$("#descrizione").val();
var dataSource = new kendo.data.DataSource({
  type: "json",
  transport: {
              read: function (options) {
                  $.ajax({
                      url: ajaxurl,
                      data: {
                          'action': 'WPsCRM_get_products',
                          'descrizione': descrizione
                      },
                      success: function (result) {
                          console.log(result);
                          $("#grid").data("kendoGrid").dataSource.data(result.products);

                      },
                      error: function (errorThrown) {
                          console.log(errorThrown);
                      }
                  })
              }
          },
  schema: {
      //data: "data",
      model: {
          ID: "ID",
          fields: {
              ID: { editable: false },
              codice: { editable: false },
              descrizione: { editable: false },
              listino1: { editable: false },
              }
          }
      },
  pageSize: 20,
  //total:data.length()
  });
        $("#grid").kendoGrid({
            dataSource: dataSource,
            height: 550,
            groupable: true,
            sortable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            columns: [{ field: "ID", title: "ID" }, { field: "codice", title: "<?php _e('Code','cpsmartcrm') ?>" }, { field: "descrizione", title: "<?php _e('Beschreibung','cpsmartcrm') ?>" }, { field: "listino1", title: "<?php _e('Preis','cpsmartcrm') ?>" },
              { command: [
                {
                    name: "<?php _e('Bearbeiten','cpsmartcrm') ?>",
                 click: function(e) {
                     var tr = $(e.target).closest("tr"); // get the current table row (tr)
                  // get the data bound to the current table row
                  var data = this.dataItem(tr);
                  //alert (data.ID);
                  //problema con accenti
                   // location.href="?page=smart-crm&p=articoli/form.php&ID="+data.id;
                    location.href="<?php echo home_url() ?>/wp-admin/post.php?post=" + data.ID + "&action=edit";
                 }
                }
              ]
            }
          ]
        });
    });
     $("#btn_cerca").trigger("click");
});
    </script> 
<ul class="select-action">
            <li  onClick="location.href='<?php echo home_url() ?>/wp-admin/post-new.php?post_type=services';return false;" class="bg-success" style="color:#000">
         <i class="glyphicon glyphicon-plus"></i><b> <?php _e('Neuer Gegenstand','cpsmartcrm') ?></b>
            </li>

        </ul>
<input type="button" id="btn_cerca" value="" style="display:none">
<div id="grid"></div> 
