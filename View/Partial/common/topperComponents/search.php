<searchtopper>
        <div class="col-sm-12">
            <div class="input-group">
                <form method="get" id="forumsearch" action="/forum/search/">
                    <input name="query" class="form-control" id="search" value="<?=$_GET['query'] ?? "" ?>">
                </form>
                <span style="cursor:pointer;" class="input-group-addon" onclick="document.getElementById('forumSearch').submit(); "><i
                            class="fa fa-search" aria-hidden="true"></i></span>
            </div>
        </div>
</searchtopper>