<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Display legend help in customers list
 */
function WPsCRM_HTML_customersLegend(){
	ob_start();
?>
<ul class="select-action">
	<li onclick="location.href='<?php echo admin_url('admin.php?page=smart-crm&p=clienti/form.php')?>';return false;" class="_newCustomer bg-success" style="color:#000">
		<i class="glyphicon glyphicon-user"></i>
		<b>
			<?php _e('New customer','cpsmartcrm') ?>
		</b>
	</li>
	<span style="float:right;">
		<li class="no-link" style="margin-top:4px">
			<span class="btn btn-info _flat">
				<i class="glyphicon glyphicon-tag"></i>= <?php _e('TODO','cpsmartcrm') ?>
			</span>
			<span class="btn btn_appuntamento_1 _flat">
				<i class="glyphicon glyphicon-pushpin"></i>= <?php _e('APPOINTMENT','cpsmartcrm') ?>
			</span>
			<span class="btn btn-primary _flat">
				<i class="glyphicon glyphicon-option-horizontal"></i>= <?php _e('ACTIVITY','cpsmartcrm') ?>
			</span>

		</li>
	</span>
</ul>
<?php
	echo ob_get_clean();
	return;
}
add_action('WPsCRM_customersLegend','WPsCRM_HTML_customersLegend',9);

/**
 * Display the main grid for customers URL: admin.php?page=smart-crm&p=clienti/list.php
 */
function WPsCRM_JS_display_customerGrid($delete_nonce){
	$options=get_option('CRM_clients_settings');
	ob_start();
?>
	    $("#grid").kendoGrid({
        dataSource: dataSource,
		scrollable: true,
        dataBound:clientsDatabound,
		noRecords: {
			template: "<h4 style=\"text-align:center;padding:5%\"><?php _e('No CUSTOMERS in database','cpsmartcrm')?></h4>"
    	},
        height: gridheight,
        groupable: false,
        sortable: true,
        pageable:
            {
                pageSizes: [20, 50, 100],
                messages:
                    {
                        display: "<?php _e('Showing','cpsmartcrm') ?> {0}-{1}  <?php _e('of','cpsmartcrm') ?> {2} <?php _e('total','cpsmartcrm') ?>",
                        of: "<?php _e('of','cpsmartcrm') ?> {0}",
                        itemsPerPage: "<?php _e('Clients per page','cpsmartcrm') ?>",
                        first: "<?php _e('First page','cpsmartcrm') ?>",
                        last: "<?php _e('Last page','cpsmartcrm') ?>",
                        next: "<?php _e('Next','cpsmartcrm') ?>",
                        previous: "<?php _e('Prev.','cpsmartcrm') ?>",
                        refresh: "<?php _e('Reload','cpsmartcrm') ?>",
                        morePages: "<?php _e('More','cpsmartcrm') ?>"
                    },
                refresh: true,
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
        columns: [
            {
            field: "ID_clienti",
            title: "<?php _e('ID','cpsmartcrm') ?>",
			width:"50px",hidden:true
            },
            {
            field: "ragione_sociale",
            title: "<?php _e('Business Name','cpsmartcrm') ?>",
            width:"150px",
            },
            {
            field: "indirizzo",
            title: "<?php _e('Address','cpsmartcrm') ?>",
                width:"150px"
            },
            {field: "cod_fis",
            title: "<?php _e('Fiscal code','cpsmartcrm') ?>",
                width:"100px"
            },
            {
            field: "p_iva",
            title: "<?php _e('VAT number','cpsmartcrm') ?>",
                width:"100px"
            },
            {
            field: "telefono1",
            title: "<?php _e('Phone','cpsmartcrm') ?>",
            width:"80px"
            },
            {
            field: "email",
            title: "<?php _e('Email','cpsmartcrm') ?>",
			width:"100px"
            },
		<?php
			if( isset( $options['gridShowCat'] ) && $options['gridShowCat'] == 1 ) :
		?>
			{
            field: "categoria",
            title: "<?php _e('Categories','cpsmartcrm') ?>",
			width:"100px"
            },
		<?php
		 endif;
			if( isset(  $options['gridShowInt'] ) && $options['gridShowInt']  == 1 ) :
		?>
			{
            field: "interessi",
            title: "<?php _e('Interests','cpsmartcrm') ?>",
			width:"100px"
            },
		<?php
		 endif;
			if( isset( $options['gridShowOr'] ) && $options['gridShowOr'] == 1 ) :
		?>
			{
            field: "provenienza",
            title: "<?php _e('Origin','cpsmartcrm') ?>",
			width:"100px"
            },
		<?php
		 endif;
		?>{field:"privileges",hidden:true},
            {width: "300px" ,
            command: [
				{
					name: "<?php _e('Edit','cpsmartcrm') ?>",
					click: function(e) {
						var tr = $(e.target).closest("tr"); // get the current table row (tr)
					    var data = this.dataItem(tr);
					    location.href="<?php echo admin_url('admin.php?page=smart-crm&p=clienti/form.php&ID=')?>"+data.ID_clienti;
					},

					className: "btn _flat"
				},
				{
					name: "<?php _e('Delete','cpsmartcrm') ?>",
					click: function(e) {
				        if (!confirm("<?php _e('Confirm delete','cpsmartcrm') ?>?"))
					        return false;
						var tr = $(e.target).closest("tr"); // get the current table row (tr)
					    var data = this.dataItem(tr);
					    location.href="<?php echo admin_url('admin.php?page=smart-crm&p=clienti/delete.php&ID=')?>"+ data.ID_clienti + "&security=<?php echo $delete_nonce ?>";
					},
					className: "btn btn-danger _flat"
				},
				{
					name: "todo",
					click: function(e) {
					    var tr = $(e.target).closest("tr"), data = this.dataItem(tr), i = $(e.target).offset();
					    $("#dialog_todo").attr('data-fkcliente', data.ID_clienti);
					    if ($('.nome_cliente').length){
						    $('.nome_cliente').html(data.ragione_sociale);
                        }
					    var todoWindow = $("#dialog_todo").data('kendoWindow')
					    todoWindow.title("<?php _e('Add todo for Customer:','cpsmartcrm') ?>" + data.ragione_sociale);
					    todoWindow.center().open();
					},

					className: "btn btn-info _flat"
				},
				{
					name: "appointment",
					click: function(e) {
					    var tr = $(e.target).closest("tr"), data = this.dataItem(tr), i = $(e.target).offset();
					    $("#dialog_appuntamento").attr('data-fkcliente', data.ID_clienti);
					    if ($('.nome_cliente').length){
						    $('.nome_cliente').html(data.ragione_sociale);
                        }
					    var appWindow = $("#dialog_appuntamento").data('kendoWindow');
					    appWindow.title("<?php _e('Add appointment for Customer:','cpsmartcrm') ?>" + data.ragione_sociale);
					    appWindow.center().open();
					},
					className: "btn btn_appuntamento_1 _flat"
				},
				{
					name: "activity",
					click: function(e) {
						var tr = $(e.target).closest("tr"), data = this.dataItem(tr), i = $(e.target).offset();
						$("#dialog_attivita").attr('data-fkcliente', data.ID_clienti);
						if ($('.nome_cliente').length){
							$('.nome_cliente').html(data.ragione_sociale);
                        }
						var actWindow = $("#dialog_attivita").data('kendoWindow');
						actWindow.title("<?php _e('Add activity for Customer:','cpsmartcrm') ?>" + data.ragione_sociale);
						actWindow.center().open();
					},
					className: "btn btn-primary _flat"
				}
            ]
        }
        ]
    });
<?php
	echo ob_get_clean();
}
add_action('WPsCRM_customerGrid','WPsCRM_JS_display_customerGrid',9,1);

/**
 * Sets the datasource used in customer grid
 */
function WPsCRM_JS_customer_datasource(){
	ob_start();
?>
var dataSource = new kendo.data.DataSource({
	transport: {
	    read: function (options) {
	        jQuery.ajax({
	            url: ajaxurl,
				type:'GET',
	            data: {
	                'action': 'WPsCRM_get_clients2',
	            },
	            success: function (result) {
	                console.log(result);
					jQuery("#grid").data("kendoGrid").dataSource.data(result.clients);

	            },
	            error: function (errorThrown) {
	                console.log(errorThrown);
	            }
	        })
	    },
		schema: {
			data: function (response) {
				return  result.clients
			},
			total: function (data) {
				return result.clients.length;
			}
		}
	},
	pageSize: 50,
});
<?php
	echo ob_get_clean();
}
add_action('WPsCRM_customer_datasource','WPsCRM_JS_customer_datasource',9);


/**
 * Process the table row after databound
 */
function WPsCRM_JS_databound_customerGrid(){
	ob_start();
?>
function clientsDatabound() {
	var gridRows = this.tbody.find("tr:not(.k-grouping-row)");
	gridRows.each(function (e) {
		var cells = jQuery(this).find('td').length;
		var commandsCell = jQuery(this).find("td:last-child");
		var todo = commandsCell.find(':nth-child(3)').html();
		commandsCell.find(':nth-child(3)').html('<i class="glyphicon glyphicon-tag"></i>').attr('title',"<?php _e('New TODO','cpsmartcrm') ?>");
		var app = commandsCell.find(':nth-child(4)').html();
		commandsCell.find(':nth-child(4)').html('<i class="glyphicon glyphicon-pushpin"></i>').attr('title', "<?php _e('NEW APPOINTMENT','cpsmartcrm') ?>");
		var act = commandsCell.find(':nth-child(5)').html();
		commandsCell.find(':nth-child(5)').html('<i class="glyphicon glyphicon-option-horizontal"></i>').attr('title', "<?php _e('NEW ACTIVITY','cpsmartcrm') ?>");
		})
	}
<?php
	echo ob_get_clean();
}
add_action('WPsCRM_databound_customerGrid','WPsCRM_JS_databound_customerGrid',9);