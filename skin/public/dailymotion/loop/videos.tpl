{strip}
    {if isset($data.id)}
        {$data = [$data]}
    {/if}
{/strip}
{if is_array($data) && !empty($data)}
    {foreach $data as $item}
        <div class="embed-responsive embed-responsive-16by9">
            <iframe src="https://geo.dailymotion.com/player/playerid.html?video={$item.video_id}&mute=true"
                    width="100%" height="100%"
                    allow="fullscreen; picture-in-picture"
                    allowfullscreen frameborder="0"
                    class="embed-responsive-item">
            </iframe>
        </div>

    {/foreach}
{/if}