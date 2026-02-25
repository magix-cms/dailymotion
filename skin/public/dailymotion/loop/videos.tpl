{strip}
    {if isset($data.id)}
        {$data = [$data]}
    {/if}
{/strip}
{if is_array($data) && !empty($data)}
    <div class="video-gallery">
        {foreach $data as $item}
            <div class="embed-responsive embed-responsive-16by9 video-wrapper">
                {* Bouclier transparent CSS *}
                <div class="video-overlay"></div>
                <iframe
                        src="https://geo.dailymotion.com/player/xb32f.html?video={if $item.visibility == 'private'}{$item.private_id}{else}{$item.video_id}{/if}&mute=false&queue-enable=false"
                        width="100%"
                        height="100%"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        frameborder="0"
                        class="embed-responsive-item protected-video">
                </iframe>
            </div>
        {/foreach}
    </div>
{/if}
{** Script js a mettre dans la page **}
{literal}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var videoWrappers = document.querySelectorAll('.video-wrapper');

            videoWrappers.forEach(function(wrapper) {
                // Bloquer le menu contextuel (clic droit)
                wrapper.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });

                // Optionnel : Bloquer certains raccourcis clavier (Ctrl+U, Ctrl+S)
                wrapper.addEventListener('keydown', function(e) {
                    if (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83)) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
        });
    </script>
{/literal}