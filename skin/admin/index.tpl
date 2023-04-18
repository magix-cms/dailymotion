{extends file="layout.tpl"}
{block name='head:title'}Dailymotion{/block}
{block name='body:id'}dailymotion{/block}
{block name='article:header'}
    <h1 class="h2"><a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}" title="Dailymotion">Dailymotion</a></h1>
{/block}
{block name='article:content'}
    {assign var="visibility" value=[
    'public','draft','private'
    ]}
    {if {employee_access type="view" class_name=$cClass} eq 1}
        <div class="panels row">
            <section class="panel col-ph-12">
                {if $debug}
                    {$debug}
                {/if}
                <header class="panel-header">
                    <h2 class="panel-heading h5">Gestion Api Dailymotion</h2>
                </header>
                <div class="panel-body panel-body-form">
                    <div class="mc-message-container clearfix">
                        <div class="mc-message"></div>
                    </div>
                    <div class="row">
                        <form id="dailymotion_config" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit" method="post" class="validate_form edit_form col-ph-12 col-md-8">
                            <div class="row">
                                <div class="col-ph-12 col-md-6">
                                    <div class="form-group">
                                        <label for="apikey_dm">API Key *:</label>
                                        <input type="text" class="form-control required" required id="apikey_dm" name="dailyData[apikey_dm]" value="{$daily.apikey_dm}" size="50" />
                                    </div>
                                </div>
                                <div class="col-ph-12 col-md-6">
                                    <div class="form-group">
                                        <label for="apisecret_dm">API Secret *:</label>
                                        <input type="text" class="form-control required" required id="apisecret_dm" name="dailyData[apisecret_dm]" value="{$daily.apisecret_dm}" size="50" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-ph-12 col-md-6">
                                    <div class="form-group">
                                        <label for="username_dm">Nom d'utilisateur *:</label>
                                        <input type="text" class="form-control required" required id="username_dm" name="dailyData[username_dm]" value="{$daily.username_dm}" size="50" />
                                    </div>
                                </div>
                                <div class="col-ph-12 col-md-6">
                                    <div class="form-group">
                                        <label for="password_dm">Password *:</label>
                                        <input type="password" class="form-control required" required id="password_dm" name="dailyData[password_dm]" value="{$daily.password_dm}" size="50" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-ph-12 col-md-6">
                                    <div class="form-group">
                                        <label for="visibility">{#visibility#|ucfirst} *:</label>
                                        <select name="dailyData[visibility_dm]" id="visibility" class="form-control required" required>
                                            <option value="">{#ph_visibility#|ucfirst}</option>
                                            {foreach $visibility as $key}
                                                <option value="{$key}" {if $daily.visibility == $key} selected{/if}>{#$key#|ucfirst}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="submit">
                                <button class="btn btn-main-theme pull-right" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
                                {*<button class="btn btn-main-theme pull-right" type="button" id="testApi" name="testApi" value="test">Test</button>*}
                            </div>
                        </form>
                        <div class="col-md-4">
                            <p class="text-center">
                            <a href="https://www.dailymotion.com/partner/" class="btn btn-box btn-main-theme targetblank">Dailymotion partner</a>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    {else}
        {include file="section/brick/viewperms.tpl"}
    {/if}
{/block}