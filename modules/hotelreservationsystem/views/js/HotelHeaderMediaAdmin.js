/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

/*---- AdminHotelHeaderMedia — Header Media Settings admin page ---- */
$(document).ready(function () {

    $('#wk-media-type').on('change', function () {
        var type = parseInt($(this).val(), 10);
        if (type === wkHmMediaTypeImage) {
            $('#wk-video-settings').slideUp(300);
            $('#wk-image-settings, #wk-image-panel, #wk-slider-info').slideDown(300);
            closeImgForm(false);
        } else {
            $('#wk-image-settings, #wk-image-panel, #wk-slider-info').slideUp(300);
            closeImgForm(false);
            $('#wk-video-settings').slideDown(300);
        }
    });

    $(document).on('change', 'input[name="WK_HEADER_SLIDER_AUTO_PLAY"]', function () {
        if ($(this).val() === '1') {
            $('#wk-slide-interval-group, #wk-slide-anim-group').slideDown(300);
        } else {
            $('#wk-slide-interval-group, #wk-slide-anim-group').slideUp(300);
        }
    });

    $(document).on('change', '#wk-img-form-file', function () {
        renderFileListHm(this.files);
    });

    $(document).on('click', '.wk-remove-file', function () {
        var removeIdx = parseInt($(this).data('index'), 10);
        var fileInput = document.getElementById('wk-img-form-file');
        if (!fileInput || !fileInput.files) { return; }
        try {
            var dt = new DataTransfer();
            for (var i = 0; i < fileInput.files.length; i++) {
                if (i !== removeIdx) { dt.items.add(fileInput.files[i]); }
            }
            fileInput.files = dt.files;
        } catch (_e) {
            /* DataTransfer not supported — remove display item only */
        }
        renderFileListHm(fileInput.files);
    });

    function renderFileListHm(files) {
        var $list = $('#wk-img-files-list').empty();
        if (!files || !files.length) { $list.hide(); return; }
        $list.show();
        for (var i = 0; i < files.length; i++) {
            (function (idx, file) {
                var size = file.size < 1048576
                    ? (file.size / 1024).toFixed(1) + ' KB'
                    : (file.size / 1048576).toFixed(1) + ' MB';
                var $li = $(
                    '<li>' +
                        '<span class="wk-file-name">' + escapeHtmlHm(file.name) + '</span>' +
                        '<span class="wk-file-size text-muted">(' + size + ')</span>' +
                        '<button type="button" class="btn btn-default wk-remove-file" data-index="' + idx + '" title="Remove">' +
                            '<i class="icon-trash"></i>' +
                        '</button>' +
                    '</li>'
                );
                $list.append($li);
            })(i, files[i]);
        }
    }

    hideOtherLanguage(wkHmDefaultLangId);

    $('#wk-source-type').on('change', function () {
        if ($(this).val() === 'url') {
            $('#wk-video-upload-field').fadeOut(150, function () {
                slideFromLeft($('#wk-video-url-field'));
            });
        } else {
            $('#wk-video-url-field').fadeOut(150, function () {
                slideFromLeft($('#wk-video-upload-field'));
            });
        }
    });

    function slideFromLeft($el) {
        $el.removeClass('wk-anim-slide-left').show();
        $el[0].offsetWidth; // force reflow so animation restarts cleanly
        $el.addClass('wk-anim-slide-left');
    }

    $(document).on('click', '#wk-vid-file-add-btn', function () {
        $('#wk-video-file-input').trigger('click');
    });
    $(document).on('change', '#wk-video-file-input', function () {
        var name = this.files && this.files[0] ? this.files[0].name : '';
        var $hint = $('.wk-vid-filename');
        if (name) {
            $hint.text(name).show();
        } else {
            $hint.hide();
        }
    });

    $(document).on('click', '#wk-delete-video', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var id   = $btn.data('id');
        var msg  = $btn.data('confirm') || 'Delete this video?';
        if (!confirm(msg)) {
            return;
        }
        $btn.prop('disabled', true);
        $.ajax({
            url:  wkHmCurrentIndex,
            type: 'POST',
            data: { ajax: 1, action: 'deleteMedia', id_header_media: id, token: wkHmToken },
            success: function (raw) {
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    $('#wk-current-video-wrap').hide();
                    $('#wk-add-video-wrap').show();
                    $('#wk-source-type').val('upload').trigger('change');
                    $('#wk-video-url-input').val('');
                    $('#wk-video-file-input').val('');
                    $('.wk-vid-filename').hide().text('');
                    showSuccessMessage(data.confirmations || 'Video deleted successfully.');
                } else {
                    $btn.prop('disabled', false);
                    showErrorMessage(data ? data.errors.join('<br>') : 'Delete failed.');
                }
            },
            error: function () {
                $btn.prop('disabled', false);
                showErrorMessage('Request failed.');
            }
        });
    });

    $('#wk-add-image-btn').on('click', function () {
        openImgForm('add');
    });

    $('#wk-img-file-add-btn').on('click', function () {
        $('#wk-img-form-file').trigger('click');
    });

    $(document).on('click', '.wk-edit-img', function () {
        var $row     = $(this).closest('tr.wk-img-row');
        var id       = parseInt($row.data('id'), 10);
        var tagLines = $row.data('tag-lines') || {};
        var isActive = $row.find('.list-action-enable').hasClass('action-enabled');
        var thumbSrc = $row.find('img.wk-img-thumb').attr('src') || '';
        openImgForm('edit', id, tagLines, isActive, thumbSrc);
    });

    /* ---- Bulk tag line ---- */
    $('#wk-bulk-tagline-apply').on('click', function () {
        var tagLines = {};
        $('.wk-bulk-tagline-field').each(function () {
            tagLines[$(this).data('lang')] = $.trim($(this).val());
        });
        var postData = { ajax: 1, action: 'bulkUpdateTagLines', token: wkHmToken };
        $.each(tagLines, function (langId, val) {
            postData['tag_line_' + langId] = val;
        });
        var $btn = $(this).prop('disabled', true);
        $.ajax({
            url:  wkHmCurrentIndex,
            type: 'POST',
            data: postData,
            success: function (raw) {
                $btn.prop('disabled', false);
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    var updatedTagLines = safeParseJsonHm(data.tag_lines_json) || tagLines;
                    $('#wk-image-tbody .wk-img-row').each(function () {
                        $(this).attr('data-tag-lines', data.tag_lines_json);
                        $(this).data('tag-lines', updatedTagLines);
                        var display = data.tag_line || '';
                        $(this).find('.wk-img-tagline-cell').text(
                            display.length > 50 ? display.substring(0, 50) + '...' : (display || '—')
                        );
                    });
                    showSuccessMessage(data.confirmations);
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : 'Update failed.');
                }
            },
            error: function () { $btn.prop('disabled', false); showErrorMessage('Request failed.'); }
        });
    });

    $('#wk-img-form-cancel').on('click', function () {
        closeImgForm(true);
    });

    $('#wk-img-form-upload-btn').on('click', function () {
        var fileInput = document.getElementById('wk-img-form-file');
        var files     = fileInput ? Array.prototype.slice.call(fileInput.files) : [];
        if (!files.length) {
            showErrorMessage((typeof wkHmI18n !== 'undefined') ? wkHmI18n.noFileSelected : 'Please select at least one image file.');
            return;
        }
        uploadImagesWithTagLine(files);
    });

    $('#wk-img-form-save-btn').on('click', function () {
        var id = parseInt($.trim($('#wk-img-form-id').val()), 10);
        if (id) {
            saveEditedImage(id);
        }
    });

    $(document).on('click', '.wk-img-row .list-action-enable', function (e) {
        e.preventDefault();
        var $link  = $(this);
        var $row   = $link.closest('tr.wk-img-row');
        var id     = parseInt($row.data('id'), 10);
        var active = $link.hasClass('action-enabled') ? 0 : 1;
        $.ajax({
            url:  wkHmCurrentIndex,
            type: 'POST',
            data: { ajax: 1, action: 'toggle_image_active', id_header_media: id, active: active, token: wkHmToken },
            success: function (raw) {
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    $link.toggleClass('action-enabled action-disabled');
                    $link.find('i').toggleClass('hidden');
                    showSuccessMessage(data.confirmations);
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : 'Update failed.');
                }
            },
            error: function () { showErrorMessage('Request failed.'); }
        });
    });

    $(document).on('click', '.wk-delete-img', function () {
        var id = parseInt($(this).data('id'), 10);
        $.ajax({
            url:  wkHmCurrentIndex,
            type: 'POST',
            data: { ajax: 1, action: 'deleteMedia', id_header_media: id, token: wkHmToken },
            success: function (raw) {
                var data = safeParseJsonHm(raw);
                if (data && data.success) {
                    var $row = $('#wk-image-tbody .wk-img-row[data-id="' + id + '"]');
                    if (parseInt($('#wk-img-form-id').val(), 10) === id) {
                        closeImgForm(false);
                    }
                    $row.fadeOut(250, function () {
                        $row.remove();
                        var count = $('#wk-image-tbody .wk-img-row').length;
                        $('#wk-image-count').text(count);
                        if (!count) {
                            $('#wk-no-images').show();
                        }
                        updateBulkActionsVisibility();
                    });
                } else {
                    showErrorMessage(data ? data.errors.join('<br>') : 'Delete failed.');
                }
            },
            error: function () { showErrorMessage('Request failed.'); }
        });
    });

    if ($('#wk-image-table').length) {
        var _hmDragOriginalOrder = null;
        $('#wk-image-table').tableDnD({
            dragHandle:  'dragHandle',
            onDragClass: 'myDragClass',
            onDragStart: function (table) {
                _hmDragOriginalOrder = [];
                $(table).find('tbody tr.wk-img-row').each(function () {
                    _hmDragOriginalOrder.push(parseInt($(this).data('id'), 10));
                });
            },
            onDrop: function (table) {
                var ids = [];
                $(table).find('tbody tr.wk-img-row').each(function (i) {
                    var id = parseInt($(this).data('id'), 10);
                    ids.push(id);
                    $(this).find('.positions').text(i + 1);
                });
                /* Only save when the row order actually changed */
                if (_hmDragOriginalOrder && JSON.stringify(ids) === JSON.stringify(_hmDragOriginalOrder)) {
                    return;
                }
                $.post(wkHmCurrentIndex, {
                    ajax:      1,
                    action:    'saveImagePositions',
                    image_ids: ids,
                    token:     wkHmToken
                }, function (raw) {
                    var data = safeParseJsonHm(raw);
                    if (data && data.success) {
                        showSuccessMessage(data.confirmations);
                    }
                });
            }
        });
    }

    function openImgForm(mode, id, tagLines, isActive, imgUrl) {
        $('#wk-img-form-id').val(id || '');
        $('#wk-form-upload-progress').hide();

        if ($('#wk-bulk-tagline-form').hasClass('in')) {
            $('#wk-bulk-tagline-form').collapse('hide');
        }

        if (mode === 'edit') {
            $('#wk-img-form-file-group').hide();
            $('#wk-img-files-list').hide().empty();
            $('#wk-img-form-add-active-group').hide();
            $('#wk-img-form-edit-group').show();
            if (isActive) {
                $('#wk_img_active_edit_on').prop('checked', true);
            } else {
                $('#wk_img_active_edit_off').prop('checked', true);
            }
            tagLines = tagLines || {};
            $('.wk-form-tagline-field').each(function () {
                var langId = $(this).data('lang');
                $(this).val(tagLines[langId] || '');
            });
            if (imgUrl) {
                $('#wk-img-edit-thumb').attr('src', imgUrl);
                $('#wk-img-edit-preview-group').show();
            } else {
                $('#wk-img-edit-preview-group').hide();
            }
        } else {
            $('#wk-img-form-file-group').show();
            $('#wk-img-form-add-active-group').show();
            $('#wk-img-form-edit-group').hide();
            $('#wk-img-edit-preview-group').hide();
            var fileInput = document.getElementById('wk-img-form-file');
            if (fileInput) {
                fileInput.value = '';
            }
            $('#wk-img-files-list').hide().empty();
            $('#wk_img_active_add_on').prop('checked', true);
            $('.wk-form-tagline-field').val('');
        }

        hideOtherLanguage(id_language);

        var scrollToImagePanel = function () {
            var headerH = ($('.page-head').length ? $('.page-head').outerHeight() : 46) + 10;
            $('html, body').animate({ scrollTop: $('#wk-image-panel').offset().top - headerH }, 400);
        };

        if (!$('#wk-img-form-panel').is(':visible')) {
            $('#wk-img-form-panel').slideDown(350, scrollToImagePanel);
        } else {
            scrollToImagePanel();
        }
    }

    function closeImgForm(animated) {
        if (!$('#wk-img-form-panel').is(':visible')) {
            return;
        }
        function resetForm() {
            $('#wk-img-form-id').val('');
            $('#wk-img-form-file-group').show();
            $('#wk-img-form-add-active-group').show();
            $('#wk-img-form-edit-group').hide();
            $('#wk-img-edit-preview-group').hide();
            $('#wk-img-edit-thumb').attr('src', '');
            $('#wk_img_active_add_on').prop('checked', true);
            var fileInput = document.getElementById('wk-img-form-file');
            if (fileInput) {
                fileInput.value = '';
            }
            $('#wk-img-files-list').hide().empty();
            $('.wk-form-tagline-field').val('');
            $('#wk-form-upload-progress').hide();
        }
        if (animated === false) {
            $('#wk-img-form-panel').hide();
            resetForm();
        } else {
            $('#wk-img-form-panel').slideUp(300, resetForm);
        }
    }

    function getFormTagLines() {
        var tagLines = {};
        $('.wk-form-tagline-field').each(function () {
            tagLines[$(this).data('lang')] = $.trim($(this).val());
        });
        return tagLines;
    }

    function saveEditedImage(id) {
        var tagLines = getFormTagLines();
        var active   = parseInt($('input[name="wk_img_active_edit"]:checked').val() || 0, 10);
        var postData = { ajax: 1, action: 'edit_image', id_header_media: id, active: active, token: wkHmToken };
        $.each(tagLines, function (langId, val) {
            postData['tag_line_' + langId] = val;
        });

        $.ajax({
            url:  wkHmCurrentIndex,
            type: 'POST',
            data: postData,
            success: function (raw) {
                var resp = safeParseJsonHm(raw);
                if (resp && resp.success) {
                    var $row = $('#wk-image-tbody .wk-img-row[data-id="' + id + '"]');
                    var updatedTagLines = safeParseJsonHm(resp.tag_lines_json) || tagLines;
                    $row.attr('data-tag-lines', resp.tag_lines_json);
                    $row.data('tag-lines', updatedTagLines);
                    var display = resp.tag_line || '';
                    $row.find('.wk-img-tagline-cell').text(
                        display.length > 50 ? display.substring(0, 50) + '...' : (display || '—')
                    );
                    var nowActive = parseInt(resp.active, 10);
                    var $toggle = $row.find('.list-action-enable');
                    $toggle.toggleClass('action-enabled', !!nowActive)
                           .toggleClass('action-disabled', !nowActive);
                    $toggle.find('i.icon-check').toggleClass('hidden', !nowActive);
                    $toggle.find('i.icon-remove').toggleClass('hidden', !!nowActive);
                    $toggle.attr('href', $toggle.attr('href').replace(/active=\d+/, 'active=' + (nowActive ? 0 : 1)));
                    closeImgForm(true);
                    showSuccessMessage(resp.confirmations || 'Image updated successfully.');
                } else {
                    showErrorMessage(resp ? resp.errors.join('<br>') : 'Update failed.');
                }
            },
            error: function () { showErrorMessage('Request failed.'); }
        });
    }

    function uploadImagesWithTagLine(files) {
        var index    = 0;
        var tagLines = getFormTagLines();
        var active   = parseInt($('input[name="wk_img_active_add"]:checked').val() || 1, 10);
        $('#wk-form-upload-progress').show();
        $('#wk-img-form-upload-btn').prop('disabled', true);

        function next() {
            if (index >= files.length) {
                $('#wk-form-upload-progress').hide();
                $('#wk-img-form-upload-btn').prop('disabled', false);
                closeImgForm(true);
                return;
            }
            var file = files[index++];
            var fd   = new FormData();
            fd.append('header_image_file', file);
            fd.append('ajax',   '1');
            fd.append('action', 'uploadImage');
            fd.append('token',  wkHmToken);
            fd.append('active', active);
            $.each(tagLines, function (langId, val) {
                fd.append('tag_line_' + langId, val);
            });

            $.ajax({
                url:         wkHmCurrentIndex,
                type:        'POST',
                data:        fd,
                processData: false,
                contentType: false,
                success: function (raw) {
                    var resp = safeParseJsonHm(raw);
                    if (resp && resp.success) {
                        var uploadedTagLines = safeParseJsonHm(resp.tag_lines_json) || tagLines;
                        appendImageRow(resp.id, resp.imgUrl, resp.tag_line || '', uploadedTagLines, resp.tag_lines_json || '{}', parseInt(resp.active, 10));
                    } else {
                        showErrorMessage(file.name + ': ' + (resp ? resp.errors.join(', ') : 'Upload failed.'));
                    }
                    next();
                },
                error: function () {
                    showErrorMessage(file.name + ': Request failed.');
                    next();
                }
            });
        }
        next();
    }

    function appendImageRow(id, imgUrl, tagLineDisplay, tagLines, tagLinesJson, active) {
        id     = parseInt(id, 10);
        active = active !== undefined ? !!active : true;
        var display      = tagLineDisplay || '';
        var cellText     = display.length > 50 ? display.substring(0, 50) + '...' : (display || '—');
        var pos          = parseInt($('#wk-image-count').text(), 10) + 1;
        var nextActive   = active ? 0 : 1;
        var toggleUrl    = wkHmCurrentIndex + '&ajax=1&action=toggle_image_active&id_header_media=' + id + '&active=' + nextActive + '&token=' + wkHmToken;
        var toggleClass  = active ? 'action-enabled' : 'action-disabled';
        var checkHidden  = active ? '' : ' hidden';
        var removeHidden = active ? ' hidden' : '';

        var $row = $(
            '<tr class="wk-img-row" id="wk_img_' + id + '" data-id="' + id + '"' +
                ' data-tag-lines="' + escapeAttrHm(tagLinesJson) + '">' +
                '<td class="row-selector text-center"><input type="checkbox" name="htl_header_mediaBox[]" class="noborder wk-img-checkbox" value="' + id + '"></td>' +
                '<td><img src="' + escapeHtmlHm(imgUrl) + '" class="wk-img-thumb img-thumbnail" alt=""></td>' +
                '<td class="wk-img-tagline-cell">' + escapeHtmlHm(cellText) + '</td>' +
                '<td class="pointer dragHandle center positionImage" id="td_wk_img_' + id + '">' +
                    '<div class="dragGroup"><div class="positions">' + pos + '</div></div>' +
                '</td>' +
                '<td class="center">' +
                    '<a class="list-action-enable ajax_table_link ' + toggleClass + '"' +
                        ' href="' + escapeAttrHm(toggleUrl) + '">' +
                        '<i class="icon-check' + checkHidden + '"></i>' +
                        '<i class="icon-remove' + removeHidden + '"></i>' +
                    '</a>' +
                '</td>' +
                '<td class="text-right">' +
                    '<div class="btn-group-action">' +
                        '<div class="btn-group pull-right">' +
                            '<button type="button" class="btn btn-default wk-edit-img" data-id="' + id + '">' +
                                '<i class="icon-pencil"></i>&nbsp;Edit' +
                            '</button>' +
                            '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-caret-down"></i>&nbsp;' +
                            '</button>' +
                            '<ul class="dropdown-menu">' +
                                '<li>' +
                                    '<a href="javascript:void(0);" class="wk-delete-img" data-id="' + id + '">' +
                                        '<i class="icon-trash"></i>&nbsp;Delete this image' +
                                    '</a>' +
                                '</li>' +
                            '</ul>' +
                        '</div>' +
                    '</div>' +
                '</td>' +
            '</tr>'
        );
        $row.data('tag-lines', tagLines);

        $('#wk-image-tbody').append($row);
        $('#wk-image-table').tableDnDUpdate();
        $('#wk-no-images').hide();
        $('#wk-image-count').text(parseInt($('#wk-image-count').text(), 10) + 1);
        updateBulkActionsVisibility();
    }

    function updateBulkActionsVisibility() {
        var count = $('#wk-image-tbody .wk-img-row').length;
        $('#wk-bulk-actions-row').toggle(count > 1);
    }

    function safeParseJsonHm(raw) {
        try { return JSON.parse(raw); } catch (_e) { return null; }
    }

    function escapeHtmlHm(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeAttrHm(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

});
