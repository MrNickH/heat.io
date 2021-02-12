<nav>
    <input type="checkbox" id="nav-checkbox">
    <label class="fa fa-bars" for="nav-checkbox" id="nav-bars"></label>
    <ul>
        <li <?=$_GET['P_one'] == "videos"? 'class="active"':''?>>
            <a class="link" href="/videos" >
                VIDEOS
            </a>
        </li><li class="<?=$_GET['P_one'] == "streams" ? 'active':''?> <?=($streaming = \Model\Media\Streamer::anyStreaming()) ? '':'disabled'?>">
            <?php if($streaming): ?><a class="link" href="/streams"><?php endif; ?>
                LIVE!<?=$streaming?" <i>&bull;</i>":""?>
            <?php if($streaming): ?></a><?php endif; ?>
        </li><li <?=$_GET['P_one'] == "giveaways"? 'class="active"':''?>>
            <a class="link" href="/giveaways" >
                GIVEAWAYS
            </a>
        </li><!--<li <?=$_GET['P_one'] == "news"? 'class="active"':''?>>
            <a class="link" href="/news" >
                NEWS
            </a>
        </li>--><li <?=$_GET['P_one'] == "forum"? 'class="active"':''?>>
            <a class="link" href="/forum">
                FORUM
            </a>
        </li><li <?=$_GET['P_one'] == "casinos"? 'class="active"':''?>>
            <a class="link" href="/casinos">
                CASINOS
            </a>
        </li>
    </ul>
</nav>