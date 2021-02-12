<div class="col-xs-12 text-center">

        <?php if (\SiteSession::LOU()->checkPermission('admin-panel')):?>
            <?=\Text::buttonGen('/admin','fa-toolbox','Dashboard','btn btn-success'); ?>
        <?php endif; ?>
        <?php if (\SiteSession::LOU()->checkPermission('admin-panel-mod')):?>
            <div class="btn-group">
                <?=\Text::buttonGen('','fa-tools','Mod Toolbox',"btn-danger", 'id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"'); ?>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?=\Text::buttonGen('/admin/modtools/dms','fa-comments','Mod DMs'); ?>
                    <?=\Text::buttonGen('/admin/modtools/forumreports','fa-comment-alt','Reported Forum Posts'); ?>
                </div>
            </div>
        <?php endif; ?>


        <?php if (\SiteSession::LOU()->checkPermission('admin-panel-content')):?>
            <div class="btn-group">
                    <?=\Text::buttonGen('','fa-star','Content',"btn-info", 'id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"'); ?>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?=\Text::buttonGen('/admin/videos','fa-film','Manage Videos'); ?>
                        <?=\Text::buttonGen('/admin/content/streams','fa-video','Manage Streams'); ?>
                        <?=\Text::buttonGen('/admin/giveaways','fa-pound-sign','Manage Giveaways'); ?>
                        <?=\Text::buttonGen('/admin/news','fa-pencil-alt','Manage News'); ?>
                        <?=\Text::buttonGen('/admin/forum/categories','fa-list','Manage Forum'); ?>
                        <?=\Text::buttonGen('/admin/casinos','fa-dice','Manage Casinos'); ?>
                    </div>
            </div>
        <?php endif; ?>


        <?php if (\SiteSession::LOU()->checkPermission('admin-panel-users')):?>
            <div class="btn-group">
                <?=\Text::buttonGen('','fa-users','Users',"btn-info", 'id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"'); ?>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                    <?=\Text::buttonGen('/admin/users','fa-users','Manage Users'); ?>
                    <?=\Text::buttonGen('/admin/users/permissions','fa-unlock','Permissions'); ?>
                    <?=\Text::buttonGen('/admin/roles','fa-unlock-alt','Roles (Permission Groups)'); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (\SiteSession::LOU()->checkPermission('admin-panel-stats')):?>
            <div class="btn-group">
                <?=\Text::buttonGen('','fa-chart-line','Marketing, Stats, CRO',"btn-info", 'id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"'); ?>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                    <?=\Text::buttonGen('/admin/stats/keyinfo','fa-key','Key Stats'); ?>
                    <?=\Text::buttonGen('/admin/stats/seo','fa-search-plus','SEO'); ?>
                    <?=\Text::buttonGen('/admin/stats/cro','fa-chart-line','CRO'); ?>
                    <?=\Text::buttonGen('https://search.google.com/search-console/about','fa-globe','Web Master Tools / Search Console', 'btn-warning', 'target="_blank"'); ?>
                    <?=\Text::buttonGen('https://analytics.google.com/analytics/web/','fa-chart-line','Google Analytics', 'btn-warning', 'target="_blank"'); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (\SiteSession::LOU()->checkPermission('admin-panel-emails')):?>
            <div class="btn-group">
                <?=\Text::buttonGen('','fa-envelope','Emails',"btn-info", 'id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"'); ?>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                    <?=\Text::buttonGen('/admin/testemail','fa-envelope','Test Emails'); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (\SiteSession::LOU()->checkPermission('admin-panel-management')):?>
            <div class="btn-group">
                <?=\Text::buttonGen('','fa-wrench','Site Management',"btn-info", 'id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"'); ?>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                    <?=\Text::buttonGen('/admin/management/logs','fa-scroll','Logs'); ?>
                    <?=\Text::buttonGen('/admin/contact','fa-phone','Contact Forms'); ?>
                    <?=\Text::buttonGen('/admin/management/errors','fa-skull-crossbones','Errors'); ?>
                    <?=\Text::buttonGen('/admin/management/pma','fa-database','MANUAL DATABASE CONTROL','btn-danger'); ?>
                </div>
            </div>
        <?php endif; ?>


</div>