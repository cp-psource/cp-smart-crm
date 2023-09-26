<!-- accountability standard -->
<div class="row form-group">
    <div class="col-md-12">
        <span id="btn_manual" class="btn btn-sm btn-add_desc_row _flat" onclick="add_manual_row();" style="margin-left:0px">
            <?php _e('Zeile mit Preis hinzufügen', 'cpsmartcrm') ?> &raquo;
        </span>
        <span id="btn_descriptive" class="btn btn-sm btn-add_desc_row _flat" onclick="add_descriptive_row();">
            <?php _e('Beschreibende Zeile hinzufügen', 'cpsmartcrm') ?> &raquo;
        </span>
        <?php do_action('WPsCRM_advanced_rows') ?>
        <?php
        if ($ID)
          do_action('WPsCRM_Einvoice', $ID);
        ?>
    </div>
</div>
<div class="row form-group">

    <table class="table table-striped table-bordered col-md-11" id="t_art" style="width:95%!important">
        <thead>
            <tr>
                <th>
                    <?php _e('Code', 'cpsmartcrm') ?>
                </th>
                <th>
                    <?php _e('Beschreibung', 'cpsmartcrm') ?>
                </th>
                <th>
                    <?php _e('Regel', 'cpsmartcrm') ?>
                </th>
                <th>
                    <?php _e('Menge', 'cpsmartcrm') ?>
                </th>
                <th>
                    <?php _e('Einzelpreis', 'cpsmartcrm') ?>
                </th>
                <th style="min-width:68px">
                    <?php _e('Rabatt', 'cpsmartcrm') ?>
                    <i class="glyphicon glyphicon-info-sign" style="color:darkmagenta;font-size:1.2em"></i><br />
                    % <input type="radio" name="tipo_sconto" id="tipo_sconto0" value="0" <?php echo ((isset($riga) && $riga["tipo_sconto"] == 0) || !isset($riga)) ? "checked" : "" ?> onchange="aggiornatot();">
                    &euro; <input type="radio" name="tipo_sconto" id="tipo_sconto1" value="1" <?php echo (isset($riga) && $riga["tipo_sconto"] == 1) ? "checked" : "" ?> onchange="aggiornatot();">
                </th>
                <th>
                    <?php _e('MwSt', 'cpsmartcrm') ?>
                </th>
                <th>
                    <?php _e('Total', 'cpsmartcrm') ?>
                </th>
                <th>
                    <?php _e('Aktionen', 'cpsmartcrm') ?>
                </th>
            </tr>
        </thead>
        <tbody>

            <?php
            if ($ID) {
              $i = 1;
              foreach ($qd as $rigad) {
                $art_id = $rigad["fk_articoli"];
                $descrizione = $rigad["descrizione"];
                $code = $rigad["codice"];
                if ($tipo_riga = $rigad["tipo"] == 3) {
                  ?>
                  <tr class="riga" id="r_<?php echo $i ?>">
                      <td colspan="8">
                          <input type="hidden" size="10" name="idd_<?php echo $i ?>" id="idd_<?php echo $i ?>" value="<?php echo $rigad["id"] ?>" />
                          <input type="hidden" size="10" name="id_<?php echo $i ?>" id="id_<?php echo $i ?>" value="<?php echo $art_id ?>" />
                          <input type="hidden" size="10" name="tipo_<?php echo $i ?>" id="tipo_<?php echo $i ?>" value="<?php echo $tipo_riga ?>" />
                          <textarea style="width:93%" name="descrizione_<?php echo $i ?>" id="descrizione_<?php echo $i ?>" class="descriptive_row"><?php echo stripslashes($descrizione) ?></textarea>
                      </td>
                      <td>
                          <a href="#" onclick="elimina_riga(<?php echo $rigad["id"] ?>, <?php echo $i ?>);return false;">
                              <?php _e('Löschen', 'cpsmartcrm') ?>
                          </a>
                      </td>
                  </tr>
                  <?php
                } else {
                  ?>
                  <tr class="riga" id="r_<?php echo $i ?>">
                      <td>
                          <input type="hidden" size="10" name="idd_<?php echo $i ?>" id="idd_<?php echo $i ?>" value="<?php echo $rigad["id"] ?>" />
                          <input type="hidden" size="10" name="id_<?php echo $i ?>" id="id_<?php echo $i ?>" value="<?php echo $art_id ?>" />
                          <input type="hidden" size="10" name="tipo_<?php echo $i ?>" id="tipo_<?php echo $i ?>" value="<?php echo $tipo_riga ?>" />
                          <input type="text" size="10" name="codice_<?php echo $i ?>" id="codice_<?php echo $i ?>" value="<?php echo $code ?>" />
                      </td>
                      <td>
                          <textarea name="descrizione_<?php echo $i ?>" id="descrizione_<?php echo $i ?>" style="width:93%"><?php echo stripslashes($descrizione) ?></textarea>
                      </td>
                      <td>
                          <?php
                          if ($rigad["fk_subscriptionrules"]) {
                            $sql = "SELECT * FROM " . WPsCRM_TABLE . "subscriptionrules WHERE ID=" . $rigad["fk_subscriptionrules"];
                            $rule = $wpdb->get_row($sql)->name;
                            $id_rule = $wpdb->get_row($sql)->ID;

                            echo $rule;
                            echo "<input type='hidden' name='subscriptionrules_" . $i . "' id='subscriptionrules_" . $i . "' value='" . $id_rule . "'>";
                          }
                          ?>
                      </td>
                      <td>
                          <input class="numeric" name="qta_<?php echo $i ?>" id="qta_<?php echo $i ?>" value="<?php echo $rigad["qta"] ?>" oninput="aggiornatot();" onblur="aggiornatot()" style="width:80px" />
                      </td>
                      <td>
                          <input class="numeric" name="prezzo_<?php echo $i ?>" id="prezzo_<?php echo $i ?>" value="<?php echo $rigad["prezzo"] ?>" oninput="aggiornatot()" onblur="aggiornatot()" style="width:130px" />
                      </td>
                      <td>
                          <input class="numeric" name="sconto_<?php echo $i ?>" id="sconto_<?php echo $i ?>" value="<?php echo $rigad["sconto"] ?>" oninput="aggiornatot()" onblur="aggiornatot()" style="width:80px" />
                      </td>
                      <td>
                          <input class="numeric" name="iva_<?php echo $i ?>" id="iva_<?php echo $i ?>" value="<?php echo $rigad["iva"] ?>" oninput="aggiornatot()" onblur="aggiornatot()" style="width:80px" />
                      </td>
                      <td>
                          <input class="numeric" size="10" name="totale_<?php echo $i ?>" id="totale_<?php echo $i ?>" value="<?php echo $rigad["totale"] ?>" style="width:130px" />
                      </td>
                      <td>
                          <button type="button" onclick="elimina_riga(<?php echo $rigad["id"] ?>, <?php echo $i ?>)">
                              <?php _e('Löschen', 'cpsmartcrm') ?>
                          </button>
                      </td>
                  </tr>
                  <?php
                }
                $i++;
              }
            }
            ?>
        </tbody>
    </table>

</div>
<div class="row form-group">
    <label class="col-sm-1 control-label">
        <?php _e('Gesamtnetto', 'cpsmartcrm') ?>
    </label>
    <div class="col-sm-3">
        <input class="numericreadonly" name="totale_imponibile" id='totale_imponibile' value="<?php if (isset($tot_imp)) echo $tot_imp ?>" readonly />
    </div>
    <label class="col-sm-1 control-label">
        <?php _e('Gesamtsteuer', 'cpsmartcrm') ?>
    </label>
    <div class="col-sm-2">
        <input data-role="numerictextbox" class="numericreadonly" name="totale_imposta" id='totale_imposta' value="<?php if (isset($totale_imposta)) echo $totale_imposta ?>" readonly />
    </div>
    <label class="col-sm-1 control-label">
        <?php _e('Gesamt', 'cpsmartcrm') ?>
    </label>
    <div class="col-sm-2">
        <input data-role="numerictextbox" class="numericreadonly" name="totale" id='totale' value="<?php if (isset($totale)) echo $totale ?>" readonly />
    </div>
</div>
<script>
  var def_iva = "<?php if (isset($def_iva)) echo $def_iva ?>", cassa = "<?php if (isset($cassa)) echo $cassa ?>", rit_acconto = "<?php if (isset($rit_acconto)) echo $rit_acconto ?>";
  jQuery(document).ready(function ($) {
      var tooltip = $(".glyphicon-info-sign").kendoTooltip({
          width: 460,
          position: "top",
          content: "<?php _e('Der Rabattmodus ( % oder Wert) gilt für das gesamte Dokument und berücksichtigt selbstverständlich die Zeilenmengen (z. B. 5 Euro Rabatt auf 10 Artikel = 50)', 'cpsmartcrm') ?>"
      }).data("kendoTooltip");

      $('.numeric').each(function () {
          console.log(kendo.toString(parseFloat($(this).val()), "n"));
          var v = kendo.toString(parseFloat($(this).val()), "n");
          $(this).kendoNumericTextBox({
              decimals: 2,
              format: "n2",
              min: 0,
              spinners: false,
              step: 0,
              culture: "<?php echo WPsCRM_CULTURE ?>"
          }).data("kendoNumericTextBox").value(v);
      })
      $('.numericreadonly').each(function () {
          var v = kendo.toString(parseFloat($(this).val()), "n");
          $(this).kendoNumericTextBox({
              decimals: 2,
              format: "n2",
              min: 0,
              spinners: false,
              step: 0,
              culture: "<?php echo WPsCRM_CULTURE ?>"
          }).data("kendoNumericTextBox").value(v);
      })

      $('#calculate').on('click', function () {
          var amount = $('#reverseAmount').val();
          var refund = $('#reverseRefund').val();
          var tipo_cliente = $('#tipo_cliente').val();
          if (amount != "")
          {
              jQuery.ajax({
                  url: ajaxurl,
                  data: {
                      action: 'WPsCRM_reverse_invoice',
                      amount: amount,
                      refund: refund,
                      accountability: 0,
                      tipo_cliente: tipo_cliente
                  },
                  success: function (result) {
                      console.log(result);
                      result = kendo.toString(parseFloat(result), "n")
                      if (jQuery('#t_art > tbody > tr').length) {
                          var last = jQuery('#t_art > tbody > tr').last().attr("id");
                          var last_id = last.split("_");
                          var n_row = parseInt(last_id[1]) + 1;
                      } else {
                          var n_row = 1;
                      }
                      jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" name="tipo_' + n_row + '" id="tipo_' + n_row + '" value="2"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value=""></td><td><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td></td><td><input data-role="numerictextbox" class="numeric" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()" oninput="aggiornatot()" value="1"  style="width:80px"></td><td><input data-role="numerictextbox" class="numeric" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value="' + result + '"  onblur="aggiornatot()"  oninput="aggiornatot()"  style="width:130px"></td><td><input data-role="numerictextbox" class="numeric" name="sconto_' + n_row + '" id="sconto_' + n_row + '"  onblur="aggiornatot()"  oninput="aggiornatot()"  style="width:80px"></td><td><input data-role="numerictextbox" class="numeric" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + def_iva + '"  onblur="aggiornatot()" oninput="aggiornatot()"  style="width:80px"></td><td><input data-role="numerictextbox" class="numeric" name="totale_' + n_row + '" id="totale_' + n_row + '" value=""  style="width:130px"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Löschen', 'cpsmartcrm') ?></button></td></tr>');
                      jQuery("#r_" + n_row + " .numeric").kendoNumericTextBox({
                          decimals: 2,
                          format: "n2",
                          spinners: false,
                          step: 0,
                          culture: "<?php echo WPsCRM_CULTURE ?>"
                      })
                      aggiornatot();
                      window.sessionStorage.setItem('ref_row', n_row);
                      if (refund)
                      {
                          //var n_row = jQuery('#t_art > tbody > tr').length + 1;
                          if (jQuery('#t_art > tbody > tr').length) {
                              var last = jQuery('#t_art > tbody > tr').last().attr("id");
                              var last_id = last.split("_");
                              var n_row = parseInt(last_id[1]) + 1;
                          } else {
                              var n_row = 1;
                          }
                          jQuery('#t_art').append('<tr class="riga riga_refund" id="r_' + n_row + '"><td colspan="3"><input type="hidden" name="tipo_' + n_row + '" id="tipo_' + n_row + '" value="4"><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td><input data-role="numerictextbox" class="numeric" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()" oninput="aggiornatot()" value="1"  style="width:80px"></td><td><input data-role="numerictextbox" class="numeric" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '"  value="' + refund + '" onblur="aggiornatot()"  oninput="aggiornatot()"  style="width:130px"></td><td></td><td></td><td><input data-role="numerictextbox" class="numeric" name="totale_' + n_row + '" id="totale_' + n_row + '" value="' + refund + '"  style="width:80px"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Löschen', 'cpsmartcrm') ?></button></td></tr>');
                          jQuery("#r_" + n_row + " .numeric").kendoNumericTextBox({
                              decimals: 2,
                              format: "n2",
                              spinners: false,
                              step: 0,
                              culture: "<?php echo WPsCRM_CULTURE ?>"
                          })
                      }
                      window.sessionStorage.setItem('tmp_amount', amount);
                      $('#reverseCalculator').data('kendoWindow').close();
                      aggiornatot();
                  },
                  error: function (errorThrown) {
                      console.log(errorThrown);
                  }
              })
          }
      })
  })
  /*function addRow(id, codice, descrizione, iva, prezzo, arr_rules, rule)
  {
      var n_row = jQuery('#t_art > tbody > tr').length + 1;
      var s_select = '<select name="subscriptionrules_' + n_row + '" id="subscriptionrules_' + n_row + '"><option value=""></option>';
      if (arr_rules != null)
          for (i = 0; i < arr_rules.length; i++)
          {
              if (rule != 0 && arr_rules[i].ID == rule)
                  is_sel = "selected";
              else
                  is_sel = "";
              s_select += '<option value="' + arr_rules[i].ID + '" ' + is_sel + '>' + arr_rules[i].name + '</option>';
          }
      s_select += '</select>';
      jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" id="tipo_' + n_row + '" name="tipo_' + n_row + '" value="1"><input type="hidden" name="id_' + n_row + '" value="' + id + '"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value="' + codice + '"></td><td><textarea  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" style="width:93%" class="descriptive_row">' + descrizione + '</textarea></td><td>' + s_select + '</td><td><input data-role="numerictextbox" class="numeric" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '" oninput="aggiornatot()" onblur="aggiornatot()" value="1" /></td><td><input data-role="numerictextbox" class="numeric" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value="' + prezzo + '" oninput="aggiornatot()" onblur="aggiornatot()"></td><td><input data-role="numerictextbox" class="numeric" size="4" name="sconto_' + n_row + '" id="sconto_' + n_row + '" size="5" oninput="aggiornatot()" onblur="aggiornatot()"></td><td><input data-role="numerictextbox" size="4" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + iva + '" size="5" oninput="aggiornatot()" onblur="aggiornatot()"></td><td><input data-role="numerictextbox" class="numeric" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Delete', 'cpsmartcrm') ?></button></td></tr>');
      aggiornatot();
  }
*/
  function aggiorna(riga) {
      //	alert(riga);
      //debugger;
      if (jQuery('#tipo_sconto0').is(':checked'))
          var tipo_sconto = 0;
      else
          var tipo_sconto = 1;
      var qta = jQuery("#qta_" + riga).data("kendoNumericTextBox").value();   
      var pre = jQuery("#prezzo_" + riga).data("kendoNumericTextBox").value();
      if (document.getElementById("sconto_" + riga))
          var sconto = jQuery("#sconto_" + riga).data("kendoNumericTextBox").value()
      if (document.getElementById("iva_" + riga))
          var iva = document.getElementById("iva_" + riga).value;
//      var tot = document.getElementById("totale_" + riga);
      if (sconto > 0)
      {
          if (tipo_sconto == 0)
              var pre_sc = pre - (pre * sconto / 100);
          else
              var pre_sc = pre - sconto;
      } else
          var pre_sc = pre;
      var totale = qta * pre_sc;
      if (parseInt(iva) > 0)
          totale = totale + (totale * iva / 100);
      totale = Math.round(totale * 100) / 100;
      var v = kendo.toString(parseFloat(totale), "n");
      jQuery("#totale_" + riga).data("kendoNumericTextBox").value(v);
      //aggiornatot();
  }

  function aggiornatot() {
      // debugger;
      if (jQuery('#t_art > tbody > tr').length) {
          var last = jQuery('#t_art > tbody > tr').last().attr("id");
          var last_id = last.split("_");
          var n_row = parseInt(last_id[1]);
      } else {
          var n_row = 0;
      }
      var form = document.forms["form_insert"];
      var totaleimp = 0;
      var totale = 0;
      var totale_imposta = 0;
      var totale_rimborso = 0;
      if (jQuery('#tipo_sconto0').is(':checked'))
          var tipo_sconto = 0;
      else
          var tipo_sconto = 1;
      for (i = 1; i <= n_row; i++) {
          if (tot = document.getElementById("totale_" + i)) {
              aggiorna(i);
              var tipo = document.getElementById("tipo_" + i).value;
              var qta = jQuery("#qta_" + i).data("kendoNumericTextBox").value();
              var pre = jQuery("#prezzo_" + i).data("kendoNumericTextBox").value();
              if (document.getElementById("iva_" + i))
                  var iva = jQuery("#iva_" + i).data("kendoNumericTextBox").value();
              if (document.getElementById("sconto_" + i))
                  var sconto = jQuery("#sconto_" + i).data("kendoNumericTextBox").value();
              if (sconto > 0)
              {
                  if (tipo_sconto == 0)
                      var pre_sc = pre - (pre * sconto / 100);
                  else
                      var pre_sc = pre - sconto;
              } else
              {
                  var pre_sc = pre;
              }
              var totaleriga = qta * pre_sc;
              if (tipo != 4)
              {
                  totale_imposta = (parseFloat(totale_imposta) + parseFloat(totaleriga * iva / 100)).toFixed(2);
                  totaleimp = (parseFloat(totaleimp) + parseFloat(totaleriga)).toFixed(2);
              } else
                  totale_rimborso = (parseFloat(totale_rimborso) + parseFloat(totaleriga)).toFixed(2);

          }
      }
      totale = parseFloat(totaleimp).toFixed(2);
      //totale = Math.round((totale) * 100) / 100;
      // totale_imposta = Math.round((totale_imposta) * 100) / 100;
      totale_imposta = parseFloat(totale_imposta).toFixed(2);
      //var totalone = Math.round((totale + totale_imposta + totale_rimborso) * 100) / 100;
      var totalone = (parseFloat(totale) + parseFloat(totale_imposta) + parseFloat(totale_rimborso)).toFixed(2);
      //totalone = Math.round((totalone) * 100) / 100;
      if (tmp_amount = sessionStorage.getItem("tmp_amount"))
      {
          //var diff = Math.round((totalone - tmp_amount) * 100) / 100;
          var diff = (totalone - parseFloat(tmp_amount)).toFixed(2);
          //console.log(diff);
          if (diff > 0)
          {
//              totale = Math.round((totale - diff) * 100) / 100;
              totale = (totale - diff).toFixed(2);
              totalone = tmp_amount;
              if (ref_row = sessionStorage.getItem("ref_row"))
              {
                  //var val_input = jQuery('#prezzo_' + ref_row).val();
                  var val_input = jQuery("#prezzo_" + ref_row).data("kendoNumericTextBox").value();
                  var newval = (val_input - diff).toFixed(2);
                  console.log(val_input);
                  console.log(newval);
                  //jQuery('#prezzo_' + ref_row).val(newval);
                  jQuery("#prezzo_" + ref_row).data("kendoNumericTextBox").value(kendo.toString(parseFloat(newval), "n"));
              }
          } else if (diff < 0)
          {
              //console.log(totale_imposta);
              totale_imposta = (totale_imposta - diff).toFixed(2);
              totalone = parseFloat(tmp_amount).toFixed(2);
          }
      }
      //totaleimp = Math.round(totaleimp * 100) / 100;
      totaleimp = parseFloat(totaleimp).toFixed(2);
//      totale_imposta = Math.round((totale_imposta) * 100) / 100;
      totale_imposta = parseFloat(totale_imposta).toFixed(2);
      //debugger;
      if (totaleimp > 0)
      {
          jQuery("#totale_imponibile").data("kendoNumericTextBox").value(kendo.toString(parseFloat(totaleimp), "n"));
          jQuery("#totale_imposta").data("kendoNumericTextBox").value(kendo.toString(parseFloat(totale_imposta), "n"));
          jQuery("#totale").data("kendoNumericTextBox").value(kendo.toString(parseFloat(totalone), "n"));
          //   form.elements["totale_imponibile"].value = totaleimp.toFixed(2);
          //   form.elements["totale_imposta"].value = totale_imposta.toFixed(2);
          //   form.elements["totale"].value = totalone.toFixed(2);
          if (form.elements["quotation_value"]) {
              //form.elements["quotation_value"].value = totaleimp.toFixed(2);
              jQuery("#quotation_value").data("kendoNumericTextBox").value(kendo.toString(parseFloat(totaleimp), "n"));
          }
      } else
      {
          jQuery("#totale_imponibile").data("kendoNumericTextBox").value(kendo.toString(parseFloat(0), "n"));
          jQuery("#totale_imposta").data("kendoNumericTextBox").value(kendo.toString(parseFloat(0), "n"));
          jQuery("#totale").data("kendoNumericTextBox").value(kendo.toString(parseFloat(0), "n"));
          // form.elements["totale_imponibile"].value = 0;
          // form.elements["totale_imposta"].value = 0;
          //  form.elements["totale"].value = 0;
          if (form.elements["quotation_value"]) {
              //form.elements["quotation_value"].value = 0;
              jQuery("#quotation_value").data("kendoNumericTextBox").value(kendo.toString(parseFloat(0), "n"));

          }
      }
  }

  function elimina_riga(id_art, riga) {
      if (!confirm("<?php _e('Löschen bestätigen? Der Löschvorgang wird erst wirksam, wenn Du das Dokument speicherst', 'cpsmartcrm') ?>"))
          return false;

      if (id_art) {
          jQuery.ajax({
              url: ajaxurl,
              data: {
                  action: 'WPsCRM_delete_document_row',
                  row_id: id_art,
                  security: '<?php echo $delete_nonce ?>'
              },
              success: function (result) {
                  console.log(result);
                  jQuery('#r_' + riga).find("input").remove();
                  jQuery('#r_' + riga).remove();
                  if (jQuery('#t_art > tbody > tr').length) {
                      aggiornatot();
                  } else {
                      jQuery("#totale_imponibile").data("kendoNumericTextBox").value(kendo.toString(parseFloat(0), "n"));
                      jQuery("#totale_imposta").data("kendoNumericTextBox").value(kendo.toString(parseFloat(0), "n"));
                      jQuery("#totale").data("kendoNumericTextBox").value(kendo.toString(parseFloat(0), "n"));
                  }
              },
              error: function (errorThrown) {
                  console.log(errorThrown);
              }
          })
      } else {
          jQuery('#r_' + riga).find("input").remove();
          jQuery('#r_' + riga).remove();
          aggiornatot();
      }
  }

  function add_manual_row()
  {
      jQuery.ajax({
          url: ajaxurl,
          data: {
              'action': 'WPsCRM_get_product_manual_info'
          },
          success: function (result) {
              console.log(result.info);
              var parseData = result.info;
              JSON.stringify(parseData);
              var iva = parseData[0].iva;
              var arr_rules = parseData[0].arr_rules;
              //console.log(arr_rules);
              if (jQuery('#t_art > tbody > tr').length) {
                  var last = jQuery('#t_art > tbody > tr').last().attr("id");
                  var last_id = last.split("_");
                  var n_row = parseInt(last_id[1]) + 1;
              } else {
                  n_row = 1;
              }
              var s_select = '<select name="subscriptionrules_' + n_row + '" id="subscriptionrules_' + n_row + '"><option value=""></option>';
              if (arr_rules != null)
                  for (i = 0; i < arr_rules.length; i++)
                  {
                      s_select += '<option value="' + arr_rules[i].ID + '">' + arr_rules[i].name + '</option>';
                  }
              s_select += '</select>';
              jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td><input type="hidden" name="tipo_' + n_row + '"  id="tipo_' + n_row + '" value="2"><input type="text" size="10" name="codice_' + n_row + '" id="codice_' + n_row + '" value=""></td><td><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td>' + s_select + '</td><td><input class="numeric" size="4" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()"  oninput="aggiornatot()" style="width:80px"></td><td><input class="numeric" size="10" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value=""  onblur="aggiornatot()"  oninput="aggiornatot()"  style="width:130px"></td><td><input class="numeric" size="4" name="sconto_' + n_row + '" id="sconto_' + n_row + '" size="5"  onblur="aggiornatot()"  oninput="aggiornatot()" style="width:80px"></td><td><input class="numeric" size="4" name="iva_' + n_row + '" id="iva_' + n_row + '" value="' + iva + '" size="5"  onblur="aggiornatot()" oninput="aggiornatot()" style="width:80px"></td><td><input class="numeric" size="4" name="totale_' + n_row + '" id="totale_' + n_row + '" style="width:130px"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Löschen', 'cpsmartcrm') ?></button></td></tr>');
              jQuery("#r_" + n_row + " .numeric").kendoNumericTextBox({
                  decimals: 2,
                  format: "n2",
                  spinners: false,
                  step: 0,
                  culture: "<?php echo WPsCRM_CULTURE ?>"
              })
          },
          error: function (errorThrown) {
              console.log(errorThrown);
          }
      })
  }
  function add_descriptive_row()
  {
      if (jQuery('#t_art > tbody > tr').length) {
          var last = jQuery('#t_art > tbody > tr').last().attr("id");
          var last_id = last.split("_");
          var n_row = parseInt(last_id[1]) + 1;
      } else {
          n_row = 1;
      }

      jQuery('#t_art').append('<tr class="riga" id="r_' + n_row + '"><td colspan="7"><input type="hidden" name="tipo_' + n_row + '" value="3"><textarea  rows="1" style="width:93%" name="descrizione_' + n_row + '" id="descrizione_' + n_row + '"  class="descriptive_row"></textarea></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Löschen', 'cpsmartcrm') ?></button></td></tr>');
  }
  function annulla()
  {
      location.href = "<?php echo admin_url('admin.php?page=smart-crm&p=documenti/list.php') ?>";
  }
  
  function add_refund_row(e) {
      //var n_row = jQuery('#t_art > tbody > tr').length + 1;
      if (jQuery('#t_art > tbody > tr').length) {
          var last = jQuery('#t_art > tbody > tr').last().attr("id");
          var last_id = last.split("_");
          var n_row = parseInt(last_id[1]) + 1;
      } else {
          n_row = 1;
      }
      jQuery('#t_art').append('<tr class="riga refund_row" id="r_' + n_row + '"><td colspan="3"><input type="hidden" name="tipo_' + n_row + '" id="tipo_' + n_row + '" value="4"><textarea rows="1" style="width:93%"  name="descrizione_' + n_row + '" id="descrizione_' + n_row + '" class="descriptive_row"></textarea></td><td><input class="numeric" name="qta_' + n_row + '" id="qta_' + n_row + '" onblur="aggiornatot()" oninput="aggiornatot()" style="width:80px"></td><td><input class="numeric" name="prezzo_' + n_row + '" id="prezzo_' + n_row + '" value=""  onblur="aggiornatot()"  oninput="aggiornatot()" style="width:130px"></td><td></td><td></td><td><input class="numeric" name="totale_' + n_row + '" id="totale_' + n_row + '" style="width:130px"></td><td><button type="button"  onclick="elimina_riga(0,' + n_row + ')"><?php _e('Löschen', 'cpsmartcrm') ?></button></td></tr>');
      jQuery("#r_" + n_row + " .numeric").kendoNumericTextBox({
          decimals: 2,
          format: "n2",
          spinners: false,
          step: 0,
          culture: "<?php echo WPsCRM_CULTURE ?>"
      })
  }

</script>
