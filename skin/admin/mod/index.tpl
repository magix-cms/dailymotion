{extends file="{$extends}"}
{block name="plugin:content"}
    {if {employee_access type="view" class_name=$cClass} eq 1}
        {assign var="visibility" value=[
        'public','draft','private'
        ]}
        <div class="row">
            <div class="col-ph-12">
                {include file="section/form/progressBar.tpl"}
            </div>
            <form id="add_video" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&edit={$smarty.get.edit}&amp;mod=add&amp;plugin={$smarty.get.plugin}" method="post" enctype="multipart/form-data" class="form-gen col-ph-12">

                <div id="drop-zone" class="dropzone">
                    <div id="drop-buttons" class="drop-buttons form-group">
                        <div class="drop-text">Déposez votre vidéo ici...</div>

                        <label class="btn btn-default" for="file">
                            ou cliquez ici.. <span class="fa fa-upload"></span>
                        </label>

                        <input type="hidden" name="MAX_FILE_SIZE" value="1073741824" />
                        <input type="file" id="file" name="file" />
                        <input type="hidden" name="id" value="{$smarty.get.edit}">
                    </div>
                </div>

                <div class="form-controls-wrapper">
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="video_visibility">{#visibility#|ucfirst} *:</label>
                                <select name="video_visibility" id="video_visibility" class="form-control required" required>
                                    <option value="">{#ph_visibility#|ucfirst}</option>
                                    {foreach $visibility as $key}
                                        <option value="{$key}">{#$key#|ucfirst}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="video_channel">Catégorie Dailymotion *:</label>
                                <select name="video_channel" id="video_channel" class="form-control required" required>
                                    <option value="tech">Informatique / Tech</option>
                                    <option value="school">Formation / Éducation</option>
                                    <option value="lifestyle">Lifestyle</option>
                                    <option value="news">Actualités</option>
                                    <option value="fun">Humour</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-main-theme" type="submit" name="action" value="file" id="submit-btn" disabled>
                        {#send#|ucfirst}
                    </button>
                </div>
            </form>
            <div id="video_list" class="col-ph-12">
                {include file="mod/video.tpl" data="videos"}
            </div>
            {include file="mod/delete.tpl" plugin='dailymotion' data_type='dailymotion' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#delete_dailymotion_message#}}

        </div>
    {else}
        {include file="section/brick/viewperms.tpl"}
    {/if}
{/block}
{block name="foot"}
    {capture name="scriptForm"}{strip}
        /{baseadmin}/min/?f=
        libjs/vendor/jquery-ui-1.12.min.js,
        libjs/vendor/progressBar.min.js,
        {baseadmin}/template/js/table-form.min.js,
        plugins/dailymotion/js/dailymotion.min.js
    {/strip}{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}
    <script type="text/javascript">
        $(function() {
            var controller = "{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}";
            if (typeof dailymotion == "undefined") {
                console.log("dailymotion is not defined");
            } else
            {
                dailymotion.run(controller,globalForm,tableForm);
            }
        });
        document.getElementById('file').addEventListener('change', function() {
            const submitBtn = document.getElementById('submit-btn');
            if (this.files.length > 0) {
                submitBtn.disabled = false;
                submitBtn.classList.add('btn-success'); // Optionnel : changer la couleur
            }
        });
    </script>
{/block}
