{if !isset($info_text)}
    {$info_text = true}
{/if}
{if !isset($delete_message)}
    {$delete_message = {#modal_delete_message#}}
{/if}
{if !isset($title)}
    {$title = {#modal_delete_title#}}
{/if}
{if !isset($controller)}
    {$controller = $smarty.get.controller}
{/if}
{capture name="mod_url"}{strip}
    {$smarty.server.SCRIPT_NAME}
    ?controller={$smarty.get.controller}
    {if isset($smarty.get.action)}&action={$smarty.get.action}{/if}
    {if isset($smarty.get.edit)}&edit={$smarty.get.edit}{/if}
    {if isset($smarty.get.tabs)}&tabs={$smarty.get.tabs}{/if}
    {if isset($smarty.get.tab)}&tab={$smarty.get.tab}{/if}
    &plugin={$smarty.get.plugin}{*&mod_edit={$smarty.get.mod_edit}*}
{/strip}{/capture}
{*-- Modal --*}
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{$title}</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <p><span class="fa fa-warning"></span> <strong>{#warning#}&thinsp;!</strong> {$delete_message}</p>
                </div>
                {if $info_text}
                    <div class="help-block">{#modal_delete_info#}</div>
                {/if}
            </div>
            <div class="modal-footer">
                <form id="delete_form" class="delete_form" action="{$smarty.capture.mod_url}&mod=delete" method="post">
                    <input type="hidden" name="id" value="">
                    <button type="button" class="btn btn-info" data-dismiss="modal">{#cancel#|ucfirst}</button>
                    <button type="submit" name="delete" value="{$data_type}" class="btn btn-danger">{#remove#|ucfirst}</button>
                </form>
            </div>
        </div>
    </div>
</div>