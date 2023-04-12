{strip}
    {if isset($data.id)}
        {$data = [$data]}
    {/if}
{/strip}
{if is_array($data) && !empty($data)}
    {foreach $data as $item}
        <div class="embed-responsive embed-responsive-16by9">
            <iframe src="https://geo.dailymotion.com/player/xb32f.html?video={if !empty($item.private_id)}{$item.private_id}{else}{$item.video_id}{/if}&mute=true"
                    width="100%" height="100%"
                    allow="fullscreen; picture-in-picture"
                    allowfullscreen frameborder="0"
                    class="embed-responsive-item">
            </iframe>
        </div>

    {/foreach}
{/if}