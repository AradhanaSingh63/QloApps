{**
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
*}


<div class="alert alert-info" id="wk-slider-info"{if $config.WK_HEADER_MEDIA_TYPE != HotelHeaderMedia::MEDIA_TYPE_IMAGE} style="display:none"{/if}>
	<i class="icon-info-circle"></i>&nbsp;
	{l s='Slider navigation and auto-slide controls take effect only when 2 or more images are active. With a single active image these settings have no visible impact on the front end.' mod='hotelreservationsystem'}
</div>

<form id="wk-header-media-form" class="form-horizontal"
	  action="{$current|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}"
	  method="post" enctype="multipart/form-data">
	<input type="hidden" name="submitHeaderMedia" value="1">

	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i>&nbsp;{l s='Header Media Settings' mod='hotelreservationsystem'}
		</div>
		<div class="panel-body">

			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Select Type' mod='hotelreservationsystem'}
				</label>
				<div class="col-lg-9">
					<select name="WK_HEADER_MEDIA_TYPE" id="wk-media-type" class="fixed-width-lg">
						<option value="{HotelHeaderMedia::MEDIA_TYPE_IMAGE}"{if $config.WK_HEADER_MEDIA_TYPE == HotelHeaderMedia::MEDIA_TYPE_IMAGE} selected{/if}>{l s='Images' mod='hotelreservationsystem'}</option>
						<option value="{HotelHeaderMedia::MEDIA_TYPE_VIDEO}"{if $config.WK_HEADER_MEDIA_TYPE == HotelHeaderMedia::MEDIA_TYPE_VIDEO} selected{/if}>{l s='Video' mod='hotelreservationsystem'}</option>
					</select>
					<p class="help-block">{l s='Choose whether the home page header shows a slideshow of images or a background video.' mod='hotelreservationsystem'}</p>
				</div>
			</div>

			<div id="wk-image-settings"{if $config.WK_HEADER_MEDIA_TYPE != HotelHeaderMedia::MEDIA_TYPE_IMAGE} style="display:none"{/if}>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Navigation Type' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<select name="WK_HEADER_SLIDER_NAV_TYPE" class="fixed-width-lg">
							<option value="{HotelHeaderMedia::NAV_TYPE_DOTS}"{if $config.WK_HEADER_SLIDER_NAV_TYPE == HotelHeaderMedia::NAV_TYPE_DOTS} selected{/if}>{l s='Dots' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderMedia::NAV_TYPE_ARROWS}"{if $config.WK_HEADER_SLIDER_NAV_TYPE == HotelHeaderMedia::NAV_TYPE_ARROWS} selected{/if}>{l s='Arrows' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderMedia::NAV_TYPE_BOTH}"{if $config.WK_HEADER_SLIDER_NAV_TYPE == HotelHeaderMedia::NAV_TYPE_BOTH} selected{/if}>{l s='Both (Dots + Arrows)' mod='hotelreservationsystem'}</option>
						</select>
						<p class="help-block">{l s='Visible only when 2 or more active images exist.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Auto Slide' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="WK_HEADER_SLIDER_AUTO_PLAY" id="auto_play_on" value="1"{if $config.WK_HEADER_SLIDER_AUTO_PLAY} checked{/if}>
							<label for="auto_play_on">{l s='Yes' mod='hotelreservationsystem'}</label>
							<input type="radio" name="WK_HEADER_SLIDER_AUTO_PLAY" id="auto_play_off" value="0"{if !$config.WK_HEADER_SLIDER_AUTO_PLAY} checked{/if}>
							<label for="auto_play_off">{l s='No' mod='hotelreservationsystem'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
				</div>

				<div class="form-group" id="wk-slide-interval-group"{if !$config.WK_HEADER_SLIDER_AUTO_PLAY} style="display:none"{/if}>
					<label class="control-label col-lg-3">{l s='Slide Interval (ms)' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<input type="text" name="WK_HEADER_SLIDER_INTERVAL" class="fixed-width-xxl"
							   value="{$config.WK_HEADER_SLIDER_INTERVAL|intval}">
						<p class="help-block">{l s='Milliseconds between slides. Min: 500. Example: 5000 = 5 s.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

				<div class="form-group" id="wk-slide-anim-group"{if !$config.WK_HEADER_SLIDER_AUTO_PLAY} style="display:none"{/if}>
					<label class="control-label col-lg-3">{l s='Slide Animation' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<select name="WK_HEADER_SLIDER_ANIM_TYPE" class="fixed-width-lg">
							<option value="{HotelHeaderMedia::ANIM_TYPE_SLIDE}"{if $config.WK_HEADER_SLIDER_ANIM_TYPE == HotelHeaderMedia::ANIM_TYPE_SLIDE} selected{/if}>{l s='Slide' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderMedia::ANIM_TYPE_FADE}"{if $config.WK_HEADER_SLIDER_ANIM_TYPE == HotelHeaderMedia::ANIM_TYPE_FADE} selected{/if}>{l s='Fade' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderMedia::ANIM_TYPE_ZOOM}"{if $config.WK_HEADER_SLIDER_ANIM_TYPE == HotelHeaderMedia::ANIM_TYPE_ZOOM} selected{/if}>{l s='Zoom' mod='hotelreservationsystem'}</option>
							<option value="{HotelHeaderMedia::ANIM_TYPE_BLUR}"{if $config.WK_HEADER_SLIDER_ANIM_TYPE == HotelHeaderMedia::ANIM_TYPE_BLUR} selected{/if}>{l s='Blur' mod='hotelreservationsystem'}</option>
						</select>
						<p class="help-block">{l s='Transition effect between slides.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

			</div>

			<div id="wk-video-settings"{if $config.WK_HEADER_MEDIA_TYPE != HotelHeaderMedia::MEDIA_TYPE_VIDEO} style="display:none"{/if}>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Tag Line' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						{foreach from=$languages item=lang}
						{if count($languages) > 1}
						<div class="translatable-field row lang-{$lang.id_lang}"{if $lang.id_lang != $defaultLangId} style="display:none"{/if}>
							<div class="col-lg-10">
						{/if}
								<input type="text"
									   name="vid_tag_line_{$lang.id_lang}"
									   class="form-control"
									   value="{if isset($videoTagLine[$lang.id_lang])}{$videoTagLine[$lang.id_lang]|escape:'html':'UTF-8'}{/if}" />
						{if count($languages) > 1}
							</div>
							<div class="col-lg-2">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
									{$lang.iso_code|upper|escape:'html':'UTF-8'} <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$languages item=lang2}
									<li><a href="javascript:hideOtherLanguage({$lang2.id_lang});">{$lang2.name|escape:'html':'UTF-8'}</a></li>
									{/foreach}
								</ul>
							</div>
						</div>
						{/if}
						{/foreach}
						<p class="help-block">{l s='Short text overlay shown on the header video. Leave empty for none.' mod='hotelreservationsystem'}</p>
					</div>
				</div>

				{if $videoItem}
				<div id="wk-current-video-wrap">
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Current Video' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<div class="wk-video-preview-card">
								{if $videoItem.source_type == 'url' || $videoItem.name|substr:0:4 == 'http' || $videoItem.name|substr:0:2 == '//'}
									<div class="wk-video-url-preview">
										<i class="icon-link"></i>
										<span class="wk-video-url-text">{$videoItem.name|escape:'html':'UTF-8'|truncate:100:'...'}</span>
									</div>
								{else}
									<video controls muted preload="metadata" class="wk-video-preview-player">
										<source src="{$imgBaseUrl|escape:'html':'UTF-8'}{$videoItem.name|escape:'html':'UTF-8'}"
												type="{$videoMimeType|escape:'html':'UTF-8'}">
										{l s='Your browser does not support the video tag.' mod='hotelreservationsystem'}
									</video>
								{/if}
							</div>
							<div class="wk-video-preview-actions">
								<button type="button" class="btn btn-danger" id="wk-delete-video"
										data-id="{$videoItem.id_header_media|intval}"
										data-confirm="{l s='Delete this video?' mod='hotelreservationsystem'}">
									<i class="icon-trash"></i>&nbsp;{l s='Delete Video' mod='hotelreservationsystem'}
								</button>
								<p class="help-block" style="margin-top:6px;">
									{l s='To replace, delete first then upload or link a new one.' mod='hotelreservationsystem'}
								</p>
							</div>
						</div>
					</div>
				</div>
				{/if}

				<div id="wk-add-video-wrap"{if $videoItem} style="display:none"{/if}>
					<div class="form-group">
						<label class="control-label col-lg-3">{l s='Video Source' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<select name="source_type" id="wk-source-type" class="fixed-width-lg">
								<option value="upload">{l s='Upload video file' mod='hotelreservationsystem'}</option>
								<option value="url">{l s='External video URL' mod='hotelreservationsystem'}</option>
							</select>
						</div>
					</div>

					<div class="form-group" id="wk-video-upload-field">
						<label class="control-label col-lg-3">{l s='Video File' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<input type="file" name="header_video_file" id="wk-video-file-input" accept=".mp4,.webm"
								   style="width:0;height:0;overflow:hidden;position:absolute;">
							<button class="btn btn-default" data-style="expand-right" data-size="s"
									type="button" id="wk-vid-file-add-btn">
								<i class="icon-folder-open"></i> {l s='Add file...' mod='hotelreservationsystem'}
							</button>
							<span class="wk-vid-filename help-block" style="display:none;margin-top:4px;"></span>
							<p class="help-block">
								{l s='Formats: .mp4, .webm' mod='hotelreservationsystem'}
								&mdash; {l s='Max:' mod='hotelreservationsystem'} {$maxUpload|escape:'html':'UTF-8'}
							</p>
						</div>
					</div>

					<div class="form-group" id="wk-video-url-field" style="display:none">
						<label class="control-label col-lg-3">{l s='External Video URL' mod='hotelreservationsystem'}</label>
						<div class="col-lg-9">
							<input type="text" name="video_url" id="wk-video-url-input" class="form-control"
								   placeholder="https://example.com/video.mp4">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>&nbsp;{l s='Save' mod='hotelreservationsystem'}
			</button>
		</div>
	</div>
</form>
<div id="wk-image-panel"{if $config.WK_HEADER_MEDIA_TYPE != HotelHeaderMedia::MEDIA_TYPE_IMAGE} style="display:none"{/if}>
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-picture"></i>&nbsp;{l s='Header Images' mod='hotelreservationsystem'}
			&nbsp;<span class="badge" id="wk-image-count">{$imageItems|count}</span>
			<span class="panel-heading-action">
				<a id="wk-add-image-btn" class="btn btn-primary" href="javascript:void(0);">
					<i class="icon-plus-sign"></i>&nbsp;{l s='Add Images' mod='hotelreservationsystem'}
				</a>
			</span>
		</div>

		<div id="wk-bulk-tagline-panel" style="padding:8px 20px;">
			<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#wk-bulk-tagline-form">
				<i class="icon-pencil"></i>&nbsp;{l s='Set tag line for all images' mod='hotelreservationsystem'}
			</button>
			<div id="wk-bulk-tagline-form" class="collapse" style="margin-top:10px;">
				<div style="display:flex; align-items:center; gap:10px;">
					<label class="control-label" style="white-space:nowrap; flex-shrink:0; margin-bottom:0; padding-top:0;">
						{l s='Tag Line (All Images)' mod='hotelreservationsystem'}
					</label>
					<div style="flex:1; min-width:0;">
						{foreach from=$languages item=lang}
						{if count($languages) > 1}
						<div class="translatable-field lang-{$lang.id_lang}"{if $lang.id_lang != $defaultLangId} style="display:none"{/if}>
							<div class="input-group">
						{/if}
								<input type="text"
									   class="form-control wk-bulk-tagline-field"
									   data-lang="{$lang.id_lang}"
									   placeholder="{l s='Tag line for all images...' mod='hotelreservationsystem'}" />
						{if count($languages) > 1}
								<div class="input-group-btn">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
										{$lang.iso_code|upper|escape:'html':'UTF-8'} <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{foreach from=$languages item=lang2}
										<li><a href="javascript:hideOtherLanguage({$lang2.id_lang});">{$lang2.name|escape:'html':'UTF-8'}</a></li>
										{/foreach}
									</ul>
								</div>
							</div>
						</div>
						{/if}
						{/foreach}
					</div>
					<button type="button" class="btn btn-primary" id="wk-bulk-tagline-apply" style="flex-shrink:0; white-space:nowrap;">
						{l s='Apply to All' mod='hotelreservationsystem'}
					</button>
				</div>
				<p class="help-block" style="margin:6px 0 0;">{l s='This will overwrite the tag line for all images in every language.' mod='hotelreservationsystem'}</p>
			</div>
		</div>

		<div id="wk-img-form-panel" style="display:none; padding:15px 20px 5px;">
			<div class="form-horizontal">
				<div class="form-group" id="wk-img-edit-preview-group" style="display:none">
					<label class="control-label col-lg-3">{l s='Editing Image' mod='hotelreservationsystem'}</label>
					<div class="col-lg-9">
						<img id="wk-img-edit-thumb" src="" class="img-thumbnail" alt=""
							 style="max-width:160px;max-height:100px;object-fit:cover;">
					</div>
				</div>

				<div class="form-group" id="wk-img-form-file-group">
					<label class="control-label col-lg-3 file_upload_label">
						{l s='Add Images' mod='hotelreservationsystem'}
					</label>
					<div class="col-lg-9">
						<input type="file" id="wk-img-form-file" multiple
							   accept="image/jpeg,image/jpg,image/png,image/webp,image/gif"
							   style="width:0;height:0;overflow:hidden;position:absolute;">
						<button class="btn btn-default" data-style="expand-right" data-size="s"
								type="button" id="wk-img-file-add-btn">
							<i class="icon-folder-open"></i> {l s='Add files...' mod='hotelreservationsystem'}
						</button>
						<ul id="wk-img-files-list" class="wk-files-list" style="display:none"></ul>
						<p class="help-block">
							{l s='Formats: .jpg, .jpeg, .png, .webp, .gif' mod='hotelreservationsystem'}
							&mdash; {l s='Recommended: 1920×600 px' mod='hotelreservationsystem'}
							&mdash; {l s='Max:' mod='hotelreservationsystem'} {$maxUpload|escape:'html':'UTF-8'}
						</p>
					</div>
				</div>

				<div class="form-group" id="wk-img-form-add-active-group">
					<label class="control-label col-lg-3">{l s='Enable' mod='hotelreservationsystem'}</label>
					<div class="col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="wk_img_active_add" id="wk_img_active_add_on" value="1" checked>
							<label for="wk_img_active_add_on">{l s='Yes' mod='hotelreservationsystem'}</label>
							<input type="radio" name="wk_img_active_add" id="wk_img_active_add_off" value="0">
							<label for="wk_img_active_add_off">{l s='No' mod='hotelreservationsystem'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-3">
						<button type="button" id="wk-img-form-upload-btn" class="btn btn-primary pull-right">
							<i class="icon-cloud-upload"></i>&nbsp;{l s='Upload' mod='hotelreservationsystem'}
						</button>
					</div>
				</div>

				<div class="form-group" id="wk-img-form-edit-group" style="display:none">
					<label class="control-label col-lg-3">{l s='Enable Image' mod='hotelreservationsystem'}</label>
					<div class="col-lg-6">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="wk_img_active_edit" id="wk_img_active_edit_on" value="1">
							<label for="wk_img_active_edit_on">{l s='Yes' mod='hotelreservationsystem'}</label>
							<input type="radio" name="wk_img_active_edit" id="wk_img_active_edit_off" value="0">
							<label for="wk_img_active_edit_off">{l s='No' mod='hotelreservationsystem'}</label>
							<a class="slide-button btn"></a>
						</span>
					</div>
					<div class="col-lg-3">
						<button type="button" id="wk-img-form-save-btn" class="btn btn-primary pull-right">
							<i class="icon-save"></i>&nbsp;{l s='Save' mod='hotelreservationsystem'}
						</button>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Tag Line' mod='hotelreservationsystem'}</label>
					<div class="col-lg-6">
						{foreach from=$languages item=lang}
						{if count($languages) > 1}
						<div class="translatable-field row lang-{$lang.id_lang}"{if $lang.id_lang != $defaultLangId} style="display:none"{/if}>
							<div class="col-lg-10">
						{/if}
								<input type="text"
									   id="wk-form-tagline-{$lang.id_lang}"
									   class="form-control wk-form-tagline-field"
									   data-lang="{$lang.id_lang}"
									   placeholder="{l s='Tag line...' mod='hotelreservationsystem'}" />
						{if count($languages) > 1}
							</div>
							<div class="col-lg-2">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
									{$lang.iso_code|upper|escape:'html':'UTF-8'} <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									{foreach from=$languages item=lang2}
									<li><a href="javascript:hideOtherLanguage({$lang2.id_lang});">{$lang2.name|escape:'html':'UTF-8'}</a></li>
									{/foreach}
								</ul>
							</div>
						</div>
						{/if}
						{/foreach}
						<p class="help-block">{l s='Short text overlay shown on this image. Leave empty for no overlay.' mod='hotelreservationsystem'}</p>
					</div>
					<div class="col-lg-3">
						<button type="button" id="wk-img-form-cancel" class="btn btn-default pull-right">
							<i class="icon-times"></i>&nbsp;{l s='Cancel' mod='hotelreservationsystem'}
						</button>
					</div>
				</div>

				<div class="form-group" id="wk-form-upload-progress" style="display:none">
					<div class="col-lg-offset-3 col-lg-9">
						<div class="alert alert-info" style="margin:0">
							<i class="icon-spinner icon-spin"></i>&nbsp;{l s='Uploading, please wait...' mod='hotelreservationsystem'}
						</div>
					</div>
				</div>

				<input type="hidden" id="wk-img-form-id" value="">

			</div>
		</div>

		<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}" id="wk-bulk-form">

		<div class="table-responsive">
			<table class="table" id="wk-image-table">
				<thead>
					<tr class="nodrag nodrop">
						<th class="center fixed-width-xs"></th>
						<th>{l s='Image' mod='hotelreservationsystem'}</th>
						<th>{l s='Tag Line' mod='hotelreservationsystem'}</th>
						<th class="center fixed-width-xs">{l s='Position' mod='hotelreservationsystem'}</th>
						<th class="center">{l s='Active' mod='hotelreservationsystem'}</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="wk-image-tbody">
					{foreach from=$imageItems item=img}
					<tr class="wk-img-row" id="wk_img_{$img.id_header_media|intval}"
						data-id="{$img.id_header_media|intval}"
						data-tag-lines="{$img.tag_lines_json|escape:'htmlall':'UTF-8'}">
						<td class="row-selector text-center">
							<input type="checkbox" name="htl_header_mediaBox[]" class="noborder wk-img-checkbox" value="{$img.id_header_media|intval}">
						</td>
						<td>
							<img src="{$imgBaseUrl|escape:'html':'UTF-8'}{$img.name|escape:'html':'UTF-8'}"
								 class="wk-img-thumb img-thumbnail" alt="">
						</td>
						<td class="wk-img-tagline-cell">
							{if $img.tag_line}{$img.tag_line|truncate:50:'...'|escape:'html':'UTF-8'}{else}&mdash;{/if}
						</td>
						<td class="pointer dragHandle center positionImage" id="td_wk_img_{$img.id_header_media|intval}">
							<div class="dragGroup">
								<div class="positions">{$img@iteration}</div>
							</div>
						</td>
						<td class="center">
							<a class="list-action-enable ajax_table_link {if $img.active}action-enabled{else}action-disabled{/if}"
							   href="{$current|escape:'html':'UTF-8'}&amp;ajax=1&amp;action=toggle_image_active&amp;id_header_media={$img.id_header_media|intval}&amp;active={if $img.active}0{else}1{/if}&amp;token={$token|escape:'html':'UTF-8'}"
							   title="{if $img.active}{l s='Enabled' mod='hotelreservationsystem'}{else}{l s='Disabled' mod='hotelreservationsystem'}{/if}">
								<i class="icon-check{if !$img.active} hidden{/if}"></i>
								<i class="icon-remove{if $img.active} hidden{/if}"></i>
							</a>
						</td>
						<td class="text-right">
							<div class="btn-group-action">
								<div class="btn-group pull-right">
									<button type="button" class="btn btn-default btn wk-edit-img"
											data-id="{$img.id_header_media|intval}">
										<i class="icon-pencil"></i>&nbsp;{l s='Edit' mod='hotelreservationsystem'}
									</button>
									<button class="btn btn-default btn dropdown-toggle" data-toggle="dropdown">
										<i class="icon-caret-down"></i>&nbsp;
									</button>
									<ul class="dropdown-menu">
										<li>
											<a href="javascript:void(0);" class="wk-delete-img"
											   data-id="{$img.id_header_media|intval}">
												<i class="icon-trash"></i>&nbsp;{l s='Delete this image' mod='hotelreservationsystem'}
											</a>
										</li>
									</ul>
								</div>
							</div>
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

		<div class="row" id="wk-bulk-actions-row"{if $imageItems|count <= 1} style="display:none"{/if}>
			<div class="col-lg-6">
				<div class="btn-group bulk-actions dropup">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						{l s='Bulk actions' mod='hotelreservationsystem'} <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'htl_header_mediaBox[]', true);return false;">
								<i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='hotelreservationsystem'}
							</a>
						</li>
						<li>
							<a href="#" onclick="javascript:checkDelBoxes($(this).closest('form').get(0), 'htl_header_mediaBox[]', false);return false;">
								<i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='hotelreservationsystem'}
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="#" onclick="sendBulkAction($(this).closest('form').get(0), 'submitBulkenableSelectionhtl_header_media');return false;">
								<i class="icon-power-off text-success"></i>&nbsp;{l s='Enable selection' mod='hotelreservationsystem'}
							</a>
						</li>
						<li>
							<a href="#" onclick="sendBulkAction($(this).closest('form').get(0), 'submitBulkdisableSelectionhtl_header_media');return false;">
								<i class="icon-power-off text-danger"></i>&nbsp;{l s='Disable selection' mod='hotelreservationsystem'}
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="#" onclick="if(confirm('{l s='Delete selected images? This cannot be undone.' mod='hotelreservationsystem' js=1}'))sendBulkAction($(this).closest('form').get(0), 'submitBulkdeletehtl_header_media');return false;">
								<i class="icon-trash"></i>&nbsp;{l s='Delete selected' mod='hotelreservationsystem'}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		</form>

		<div id="wk-no-images" class="alert alert-warning"{if $imageItems} style="display:none"{/if}>
			<i class="icon-warning-sign"></i>&nbsp;{l s='No images found. Click "Add Images" to upload.' mod='hotelreservationsystem'}
		</div>

	</div>
</div>
