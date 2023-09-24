
<ul>
	<?php
  $table=WPsCRM_TABLE."documenti";
  $sql="SELECT * FROM ".$table." where fk_clienti=$client_ID order by progressivo desc";
  $path=dirname(dirname(__FILE__));
  foreach( $wpdb->get_results( $sql ) as $record){
    $id=$record->id;
    $data=WPsCRM_culture_date_format($record->data);
    $oggetto=$record->oggetto;
    $progressivo=$record->progressivo;
    $totale=$record->totale;
	?>
    <li><a href="<?php echo get_the_permalink() ?>?id_invoice=<?=$id?>" target="_blank"><?php _e('Invoice','cpsmartcrm')?> # <?=$progressivo?> <?php _e('Date','cpsmartcrm')?>:  <?=$data?> </a></li>
    <?
  }

?>
</ul>
