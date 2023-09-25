/**
 * jQuery Form Builder Plugin
 * Copyright (c) 2009 Mike Botsko, Botsko.net LLC (http://www.botsko.net)
 * http://www.botsko.net/blog/2009/04/jquery-form-builder-plugin/
 * Originally designed for AspenMSM, a CMS product from Trellis Development
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 */
(function ($) {
    $.fn.formbuilder = function (options) {
        // Extend the configuration options with user-provided
        var defaults = {
            save_url: false,
            load_url: false,
            control_box_target: false,
            serialize_prefix: 'frmb',
            css_ol_sortable_class: 'ol_opt_sortable',
            messages: {
                save: "Save",
                add_new_field: "Add New Field...",
                text: "Text Field",
                title: "Title",
                paragraph: "Paragraph",
                checkboxes: "Checkboxes",
                radio: "Radio",
                select: "Select List",
                datetime: "Datetime",
                color: "Colorpicker",
                video: "Video",
                gallery: "Gallery",
                text_field: "Text Field",
                label: "Label",
                paragraph_field: "Paragraph Field",
                select_options: "Select Options",
                add: "Add",
                checkbox_group: "Checkbox Group",
                remove_message: "Are you sure you want to remove this element?",
                remove: "Remove",
                radio_group: "Radio Group",
                selections_message: "Allow Multiple Selections",
                hide: "Hide",
                required: "Required",
                show: "Show"
            }
        };
        var opts = $.extend(defaults, options);
        var frmb_id = 'frmb-' + $('ul[id^=frmb-]').length++;
        return this.each(function () {
            var ul_obj = $(this).append('<ul id="' + frmb_id + '" class="frmb"></ul>').find('ul');
            var field = '', field_type = '', last_id = 1, help, form_db_id;
            // Add a unique class to the current element
            $(ul_obj).addClass(frmb_id);
            // load existing form data
            if (opts.load_url) {
                /*$.getJSON(opts.load_url, function (json) {
                    form_db_id = json.form_id;
                    fromJson(json.form_structure);
                });*/
                $.ajax({
                  dataType: "json",
                  url: ajaxurl,
                  data: {
                      'action': opts.load_url
                  },
                  success: function (json) {
	                           form_db_id = json.form_id;
                             fromJson(json.form_structure);
	                        },
                  error: function (errorThrown) {
                      console.log(errorThrown);
                  }
                });
            }
            // Create form control select box and add into the editor
            var controlBox = function (target) {
                var selectOptions = [
                    { value: "0", label: opts.messages.add_new_field },
                    { value: "input_text", label: opts.messages.text },
                    { value: "textarea", label: opts.messages.paragraph },
                    { value: "checkbox", label: opts.messages.checkboxes },
                    { value: "radio", label: opts.messages.radio },
                    { value: "select", label: opts.messages.select },
                    { value: "datetime", label: opts.messages.datetime },
                    { value: "color", label: opts.messages.color },
                    { value: "video", label: opts.messages.video },
                    { value: "gallery", label: opts.messages.gallery }
                ];

                var box_content = '';
                var save_button = '';
                var box_id = frmb_id + '-control-box';
                var save_id = frmb_id + '-save-button';

                // Build the control box and search button content
                box_content = '<select id="' + box_id + '" class="frmb-control">';
                
                // Generate secure option elements
                for (var i = 0; i < selectOptions.length; i++) {
                    box_content += '<option value="' + selectOptions[i].value + '">' + selectOptions[i].label + '</option>';
                }
                
                box_content += '</select>';
                save_button = '<input type="submit" id="' + save_id + '" class="frmb-submit _saveFields" value="' + opts.messages.save + '"/>';

                // Insert the control box into page
                if (!target) {
                    $(ul_obj).before(box_content);
                } else {
                    $(target).append(box_content);
                }

                // Insert the search button
                $(ul_obj).after(save_button);

                // Set the form save action
                $('#' + save_id).click(function () {
                    save();
                    return false;
                });
                // Add a callback to the select element
                $('#' + box_id).change(function () {
                    appendNewField($(this).val());
                    $(this).val(0).blur();
                    // This solves the scrollTo dependency
                    $('html, body').animate({
                        scrollTop: $('#frm-' + (last_id - 1) + '-item').offset().top
                    }, 500);
                    return false;
                });
            }(opts.control_box_target);
            // Json parser to build the form builder
            var fromJson = function (json) {
                var values = '';
                var options = false;
                // Parse json
                $(json).each(function () {
                    // checkbox type
                    if (this.cssClass === 'checkbox') {
                        options = [this.title];
                        values = [];
                        $.each(this.values, function () {
                            values.push([this.value, this.baseline]);
                        });
                    }
                        // radio type
                    else if (this.cssClass === 'radio') {
                        options = [this.title];
                        values = [];
                        $.each(this.values, function () {
                            values.push([this.value, this.baseline]);
                        });
                    }
                        // select type
                    else if (this.cssClass === 'select') {
                        options = [this.title, this.multiple];
                        values = [];
                        $.each(this.values, function () {
                            values.push([this.value, this.baseline]);
                        });
                    }

                    else {
                        values = [this.values];
                    }
                    appendNewField(this.cssClass, values, options, this.required, this.id_field);
                });
            };
            // Wrapper for adding a new field
            var appendNewField = function (type, values, options, required, id_field) {
                field = '';
                field_type = type;
                if (typeof (values) === 'undefined') {
                    values = '';
                }
                switch (type) {
                    case 'input_text':
                        appendTextInput(values, required, id_field);
                        break;
                    case 'textarea':
                        appendTextarea(values, required);
                        break;
                    case 'checkbox':
                        appendCheckboxGroup(values, options, required, id_field);
                        break;
                    case 'radio':
                        appendRadioGroup(values, options, required);
                        break;
                    case 'select':
                        appendSelectList(values, options, required);
                        break;
                    case 'color':
                        appendColorInput(values, required);
                        break;
                    case 'datetime':
                        appendDatepicker(values, required);
                        break;
                    case 'gallery':
                        appendGallery(values, required);
                        break;
                    case 'video':
                        appendVideo(values, required);
                        break;
                }
            };
            // single line input type="text"
            var appendTextInput = function (values, required, id_field) {
                field += '<label>' + opts.messages.label + '</label>';
                field += '<input class="fld-title" id="title-' + last_id + '"  name="title-' + last_id + '" type="text" value="' + values + '" />';                
                field += '<input class="fld-title" id="id_field-' + last_id + '"  name="id_field-' + last_id + '" type="hidden" value="' + id_field + '" />';
                help = '';
                appendFieldLi(opts.messages.text, field, required, help);
            };
            //Colorpicker
            var appendColorInput = function (values, required) {
                field += '<label>' + opts.messages.label + '</label>';
                field += '<input class="fld-title" " type="text" value="' + values + '" />';
                help = 'Aggiunge un selettore di colore';
                appendFieldLi(opts.messages.color, field, required, help);
            };
            //Datetime
            var appendDatepicker = function (values, required) {
                field += '<label>' + opts.messages.label + '</label>';
                field += '<input class="fld-title" " type="text" value="' + values + '" />';
                help = 'Aggiunge un calendario';
                appendFieldLi(opts.messages.datetime, field, required, help);
            };
            //Gallery
            var appendGallery = function (values, required) {
                field += '<label>' + opts.messages.label + '</label>';
                field += '<input class="fld-title" " type="text" value="' + values + '" />';
                help = 'Aggiunge una gallery';
                appendFieldLi(opts.messages.gallery, field, required, help);
            };
            //Video
            var appendVideo = function (values, required) {
                field += '<label>' + opts.messages.label + '</label>';
                field += '<input class="fld-title" " type="text" value="' + values + '" />';
                help = 'Aggiunge un video';
                appendFieldLi(opts.messages.video, field, required, help);
            };
            // multi-line textarea
            var appendTextarea = function (values, required) {
                field += '<label>' + opts.messages.label + '</label>';
                field += '<input type="text" value="' + values + '" />';
                help = '';
                appendFieldLi(opts.messages.paragraph_field, field, required, help);
            };
            // adds a checkbox element
            var appendCheckboxGroup = function (values, options, required, id_field) {
                var title = '';
                if (typeof (options) === 'object') {
                    title = options[0];
                }
                field += '<div class="chk_group">';
                field += '<div class="frm-fld"><label>' + opts.messages.title + '</label>';
                field += '<input type="text" name="title" value="' + title + '" /></div>';
                field += '<div class="false-label">' + opts.messages.select_options + '</div>';
                field += '<div class="fields">';

                field += '<div><ol class="' + opts.css_ol_sortable_class + '">';

                if (typeof (values) === 'object') {
                    for (i = 0; i < values.length; i++) {
                        field += checkboxFieldHtml(values[i]);
                    }
                }
                else {
                    field += checkboxFieldHtml('');
                }

                field += '</ol></div>';

                field += '<div class="add-area"><a href="#" class="add add_ck">' + opts.messages.add + '</a></div>';
                field += '</div>';
                field += '</div>';
                field += '<input class="fld-title" id="id_field-' + last_id + '"  name="id_field-' + last_id + '" type="hidden" value="' + id_field + '" />';
                help = '';
                appendFieldLi(opts.messages.checkbox_group, field, required, help);

                $('.' + opts.css_ol_sortable_class).sortable(); // making the dynamically added option fields sortable.
            };
            // Checkbox field html, since there may be multiple
            var checkboxFieldHtml = function (values) {
                var checked = false;
                var value = '';
                if (typeof (values) === 'object') {
                    value = values[0];
                    checked = (values[1] === 'false' || values[1] === 'undefined') ? false : true;
                }
                field = '<li>';
                field += '<div>';
                field += '<input type="checkbox"' + (checked ? ' checked="checked"' : '') + ' />';
                field += '<input type="text" value="' + value + '" />';
                field += '<a href="#" class="remove" title="' + opts.messages.remove_message + '">' + opts.messages.remove + '</a>';
                field += '</div></li>';
                return field;
            };
            // adds a radio element
            var appendRadioGroup = function (values, options, required) {
                var title = '';
                if (typeof (options) === 'object') {
                    title = options[0];
                }
                field += '<div class="rd_group">';
                field += '<div class="frm-fld"><label>' + opts.messages.title + '</label>';
                field += '<input type="text" name="title" value="' + title + '" /></div>';
                field += '<div class="false-label">' + opts.messages.select_options + '</div>';
                field += '<div class="fields">';

                field += '<div><ol class="' + opts.css_ol_sortable_class + '">';

                if (typeof (values) === 'object') {
                    for (i = 0; i < values.length; i++) {
                        field += radioFieldHtml(values[i], 'frm-' + last_id + '-fld');
                    }
                }
                else {
                    field += radioFieldHtml('', 'frm-' + last_id + '-fld');
                }

                field += '</ol></div>';

                field += '<div class="add-area"><a href="#" class="add add_rd">' + opts.messages.add + '</a></div>';
                field += '</div>';
                field += '</div>';
                help = '';
                appendFieldLi(opts.messages.radio_group, field, required, help);

                $('.' + opts.css_ol_sortable_class).sortable(); // making the dynamically added option fields sortable. 
            };
            // Radio field html, since there may be multiple
            var radioFieldHtml = function (values, name) {
                var checked = false;
                var value = '';
                if (typeof (values) === 'object') {
                    value = values[0];
                    checked = (values[1] === 'false' || values[1] === 'undefined') ? false : true;
                }
                field = '<li>';
                field += '<div>';
                field += '<input type="radio"' + (checked ? ' checked="checked"' : '') + ' name="radio_' + name + '" />';
                field += '<input type="text" value="' + value + '" />';
                field += '<a href="#" class="remove" title="' + opts.messages.remove_message + '">' + opts.messages.remove + '</a>';
                field += '</div></li>';

                return field;
            };
            // adds a select/option element
            var appendSelectList = function (values, options, required) {
                var multiple = false;
                var title = '';
                if (typeof (options) === 'object') {
                    title = options[0];
                    multiple = options[1] === 'true' || options[1] === 'checked' ? true : false;
                }
                field += '<div class="opt_group">';
                field += '<div class="frm-fld"><label>' + opts.messages.title + '</label>';
                field += '<input type="text" name="title" value="' + title + '" /></div>';
                field += '';
                field += '<div class="false-label">' + opts.messages.select_options + '</div>';
                field += '<div class="fields">';
                field += '<input type="checkbox" name="multiple"' + (multiple ? 'checked="checked"' : '') + '>';
                field += '<label class="auto">' + opts.messages.selections_message + '</label>';

                field += '<div><ol class="' + opts.css_ol_sortable_class + '">';

                if (typeof (values) === 'object') {
                    for (i = 0; i < values.length; i++) {
                        field += selectFieldHtml(values[i], multiple);
                    }
                }
                else {
                    field += selectFieldHtml('', multiple);
                }

                field += '</ol></div>';

                field += '<div class="add-area"><a href="#" class="add add_opt">' + opts.messages.add + '</a></div>';
                field += '</div>';
                field += '</div>';
                help = '';
                appendFieldLi(opts.messages.select, field, required, help);

                $('.' + opts.css_ol_sortable_class).sortable(); // making the dynamically added option fields sortable.  
            };
            // Select field html, since there may be multiple
            var selectFieldHtml = function (values, multiple) {
                if (multiple) {
                    return checkboxFieldHtml(values);
                }
                else {
                    return radioFieldHtml(values);
                }
            };
           // Appends the new field markup to the editor
            var appendFieldLi = function (title, field_html, required, help) {
                if (required) {
                    required = required === 'checked' ? true : false;
                }
                var li = $('<li>', {
                    id: 'frm-' + last_id + '-item',
                    class: field_type
                });

                var legend = $('<div>', {
                    class: 'legend'
                });

                var deleteButton = $('<a>', {
                    id: 'del_' + last_id,
                    class: 'del-button delete-confirm',
                    href: '#',
                    title: opts.messages.remove_message
                }).append($('<span>', {
                    text: opts.messages.remove
                }));

                var titleElement = $('<strong>', {
                    id: 'txt-title-' + last_id,
                    text: title
                });

                legend.append(deleteButton, titleElement);
                li.append(legend);

                var frmHolder = $('<div>', {
                    id: 'frm-' + last_id + '-fld',
                    class: 'frm-holder'
                });

                var frmElements = $('<div>', {
                    class: 'frm-elements'
                });

                var frmFld = $('<div>', {
                    class: 'frm-fld'
                });

                var requiredLabel = $('<label>', {
                    for: 'required-' + last_id,
                    text: opts.messages.required
                });

                var requiredCheckbox = $('<input>', {
                    class: 'required',
                    type: 'checkbox',
                    value: '1',
                    name: 'required-' + last_id,
                    id: 'required-' + last_id,
                    checked: required
                });

                frmFld.append(requiredLabel, requiredCheckbox, field_html);
                frmElements.append(frmFld);
                frmHolder.append(frmElements);

                var smallElement = $('<small>', {
                    text: ' ' + help
                });

                li.append(frmHolder, smallElement);

                $(ul_obj).append(li);

                $('#frm-' + last_id + '-item').hide();
                $('#frm-' + last_id + '-item').animate({
                    opacity: 'show',
                    height: 'show'
                }, 'slow');
                last_id++;
            };
            // handle field delete links
            $('.frmb').delegate('.remove', 'click', function () {
                $(this).parent('div').animate({
                    opacity: 'hide',
                    height: 'hide',
                    marginBottom: '0px'
                }, 'fast', function () {
                    alert ("rimuovo");
                    $(this).remove();
                });
                return false;
            });
            // handle field display/hide
            $('.frmb').delegate('.toggle-form', 'click', function () {
                var target = $(this).attr("id");
                if ($(this).html() === opts.messages.hide) {
                    $(this).removeClass('open').addClass('closed').html(opts.messages.show);
                    $('#' + target + '-fld').animate({
                        opacity: 'hide',
                        height: 'hide'
                    }, 'slow');
                    return false;
                }
                if ($(this).html() === opts.messages.show) {
                    $(this).removeClass('closed').addClass('open').html(opts.messages.hide);
                    $('#' + target + '-fld').animate({
                        opacity: 'show',
                        height: 'show'
                    }, 'slow');
                    return false;
                }
                return false;
            });
            // handle delete confirmation
            $('.frmb').delegate('.delete-confirm', 'click', function () {
                var delete_id = $(this).attr("id").replace(/del_/, '');
                if (confirm($(this).attr('title'))) {
                    $('#frm-' + delete_id + '-item').animate({
                        opacity: 'hide',
                        height: 'hide',
                        marginBottom: '0px'
                    }, 'slow', function () {
                        alert ("rimuovo");
                        
                        $(this).remove();
                    });
                }
                return false;
            });
            // Attach a callback to add new checkboxes
            $('.frmb').delegate('.add_ck', 'click', function () {
                $(this).parent().before(checkboxFieldHtml());
                return false;
            });
            // Attach a callback to add new options
            $('.frmb').delegate('.add_opt', 'click', function () {
                $(this).parent().before(selectFieldHtml('', false));
                return false;
            });
            // Attach a callback to add new radio fields
            $('.frmb').delegate('.add_rd', 'click', function () {
                $(this).parent().before(radioFieldHtml(false, $(this).parents('.frm-holder').attr('id')));
                return false;
            });
            // saves the serialized data to the server
            var save = function () {
                if (opts.save_url) {
                    $.ajax({
                        datatype: "json",
                        type: "POST",
                        url: ajaxurl,
                        data: {
                            'action': opts.save_url,
                            'formdata': $(ul_obj).serializeFormList({
                            prepend: opts.serialize_prefix
                            }) + "&form_id=" + form_db_id,
                            'section': 'clienti'
                        },
                        success: function () { }
                    });
                }
            };
        });
    };
})(jQuery);
/**
 * jQuery Form Builder List Serialization Plugin
 * Copyright (c) 2009 Mike Botsko, Botsko.net LLC (http://www.botsko.net)
 * Originally designed for AspenMSM, a CMS product from Trellis Development
 * Licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * Copyright notice and license must remain intact for legal use
 * Modified from the serialize list plugin
 * http://www.botsko.net/blog/2009/01/jquery_serialize_list_plugin/
 */
(function ($) {
    $.fn.serializeFormList = function (options) {
        // Extend the configuration options with user-provided
        var defaults = {
            prepend: 'ul',
            is_child: false,
            attributes: ['class']
        };
        var opts = $.extend(defaults, options);
        if (!opts.is_child) {
            opts.prepend = '&' + opts.prepend;
        }
        var serialStr = '';
        // Begin the core plugin
        this.each(function () {
            var ul_obj = this;
            console.log("447:"+ ul_obj)
            var li_count = 0;
            var c = 1;
            $(this).children().each(function () {
                for (att = 0; att < opts.attributes.length; att++) {
                    var key = (opts.attributes[att] === 'class' ? 'cssClass' : opts.attributes[att]);
                    serialStr += opts.prepend + '[' + li_count + '][' + key + ']=' + encodeURIComponent($(this).attr(opts.attributes[att]));
                    // append the form field values
                    if (opts.attributes[att] === 'class') {
                        serialStr += opts.prepend + '[' + li_count + '][required]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input.required').is(':checked'));
                        switch ($(this).attr(opts.attributes[att])) {
                            case 'input_text':
                                serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
                                serialStr += opts.prepend + '[' + li_count + '][id_field]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=hidden]').val());
                                break;
                            case 'textarea':
                                serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
                                break;
                            case 'checkbox':
                                c = 1;
                                $('#' + $(this).attr('id') + ' input[type=text]').each(function () {
                                    if ($(this).attr('name') === 'title') {
                                        serialStr += opts.prepend + '[' + li_count + '][title]=' + encodeURIComponent($(this).val());
                                    }
                                    else {
                                        serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][value]=' + encodeURIComponent($(this).val());
                                        serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][baseline]=' + $(this).prev().is(':checked');
                                    }
                                    c++;
                                });
                                break;
                            case 'radio':
                                c = 1;
                                $('#' + $(this).attr('id') + ' input[type=text]').each(function () {
                                    if ($(this).attr('name') === 'title') {
                                        serialStr += opts.prepend + '[' + li_count + '][title]=' + encodeURIComponent($(this).val());
                                    }
                                    else {
                                        serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][value]=' + encodeURIComponent($(this).val());
                                        serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][baseline]=' + $(this).prev().is(':checked');
                                    }
                                    c++;
                                });
                                break;
                            case 'select':
                                c = 1;
                                serialStr += opts.prepend + '[' + li_count + '][multiple]=' + $('#' + $(this).attr('id') + ' input[name=multiple]').is(':checked');
                                $('#' + $(this).attr('id') + ' input[type=text]').each(function () {
                                    if ($(this).attr('name') === 'title') {
                                        serialStr += opts.prepend + '[' + li_count + '][title]=' + encodeURIComponent($(this).val());
                                    }
                                    else {
                                        serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][value]=' + encodeURIComponent($(this).val());
                                        serialStr += opts.prepend + '[' + li_count + '][values][' + c + '][baseline]=' + $(this).prev().is(':checked');
                                    }
                                    c++;
                                });
                                break;
                            case 'color':
                                serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
                                break;
                            case 'datetime':
                                serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
                                break;
                            case 'gallery':
                                serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
                                break;
                            case 'video':
                                serialStr += opts.prepend + '[' + li_count + '][values]=' + encodeURIComponent($('#' + $(this).attr('id') + ' input[type=text]').val());
                                break;
                        }
                    }
                }
                li_count++;
            });
        });
        console.log(serialStr)
        return (serialStr);
    };
})(jQuery);