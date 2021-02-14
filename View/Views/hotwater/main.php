<div class="album py-5 bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-3">
                <div class="card mb-12 box-shadow">
                    <div class="card-body">
                        <h2 class="h2 text-center">Hot Water - <?=($hwStatus)?'ON':'OFF' ?></h2>
                        <?php if($hwStatus): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fa fa-shower"></i> <strong>Hot Water ON</strong> -
                                <small class="text-muted">Came on at: $time($reason)</small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-icicles"></i> <strong>Hot Water OFF</strong>
                                <small class="text-muted">Went off at: $time($reason)</small>
                            </div>
                        <?php endif; ?>
                        <p>
                            <small class="text-muted"><strong>Next Event:</strong> Next event text at $nextEventTime</small>
                        </p>
                        <div class="align-items-center">
                            <div class="btn-group">
                                <?php if($hwStatus): ?>
                                    <a href="/hotwater/off" type="button" class="btn btn-info my-2"><i class="fa fa-icicles"></i> Turn Hot Water Off </a>
                                <?php else: ?>
                                    <a href="/hotwater/on" type="button"  class="btn btn-warning my-2"><i class="fa fa-shower"></i> Turn Hot Water On </a>
                                <?php endif; ?>
                                <a href="/hotwater/schedule" type="button"  class="btn btn-outline-primary my-2"><i class="fa fa-clock"></i> Schedule </a>
                                <a href="/hotwater/settings" type="button"  class="btn btn-outline-dark  my-2"><i class="fa fa-cogs"></i> Settings </a>
                                <a href="/hotwater/boost" type="button"  class="btn btn-warning my-2"><i class="fa fa-bolt"></i> BOOST </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>