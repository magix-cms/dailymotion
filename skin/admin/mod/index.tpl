{extends file="{$extends}"}
{block name="plugin:content"}
    {if {employee_access type="view" class_name=$cClass} eq 1}
        <div class="row">
            <div class="col-ph-12">
                {include file="section/form/progressBar.tpl"}
            </div>
            <form id="add_video" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&edit={$smarty.get.edit}&amp;mod=add&amp;plugin={$smarty.get.plugin}" method="post" enctype="multipart/form-data" class="form-gen col-ph-12">
                <div id="drop-zone">
                    Déposez votre vidéo ici...
                    <div id="drop-buttons" class="form-group">
                        <label id="clickHere" class="btn btn-default">
                            ou cliquez ici.. <span class="fa fa-upload"></span>
                            <input type="hidden" name="MAX_FILE_SIZE" value="20971520" />
                            <input type="file" id="file" name="file" value="" />
                            <input type="hidden" id="product[id]" name="id" value="{$smarty.get.edit}">
                        </label>
                        <button class="btn btn-main-theme" type="submit" name="action" value="file" disabled>{#send#|ucfirst}</button>
                    </div>
                </div>
                {*
                <label for="file"><span>Filename:</span></label>
                <input type="file" name="file" id="file" />
                <input type="hidden" name="MAX_FILE_SIZE" value="524288" />
                <input type="hidden" id="product[id]" name="id" value="{$smarty.get.edit}">
                <input type="submit" name="submit" value="Submit" />
                *}
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
            if (typeof dailymotion == "undefined") {
                console.log("dailymotion is not defined");
            } else
            {
                dailymotion.run(globalForm,tableForm)
            }
        });
    </script>
{/block}
