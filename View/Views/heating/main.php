<div class="album py-5 bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-3">
                <div class="card mb-12 box-shadow">
                    <div class="card-body">
                        <h2 class="h2 text-center">Heating - <?=($heatingStatus)?'ON':'OFF' ?></h2>
                        <?php if($heatingStatus): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fa fa-fire"></i> <strong>Heating ON</strong> -
                                <small class="text-muted">Came on at: $time($reason)</small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-primary" role="alert">
                                <i class="fa fa-snowflake"></i> <strong>Heating OFF</strong> -
                                <small class="text-muted">Went off at: $time($reason)</small>
                            </div>
                        <?php endif; ?>
                        <p>
                            <small class="text-muted"><strong>Next Event:</strong> Next event text at $nextEventTime</small>
                        </p>
                        <div class="align-items-center">
                            <div class="btn-group">
                                <?php if($heatingStatus): ?>
                                    <a href="/heating/off" type="button" class="btn btn-primary my-2"><i class="fa fa-snowflake"></i> Turn Heating Off </a>
                                <?php else: ?>
                                    <a href="/heating/on" type="button"  class="btn btn-danger my-2"><i class="fa fa-fire"></i> Turn Heating On </a>
                                <?php endif; ?>
                                <a href="/heating/schedule" type="button"  class="btn btn-outline-primary my-2"><i class="fa fa-clock"></i> Schedule </a>
                                <a href="/heating/settings" type="button"  class="btn btn-outline-dark  my-2"><i class="fa fa-cogs"></i> Settings </a>
                                <a href="/heating/boost" type="button"  class="btn btn-warning my-2"><i class="fa fa-bolt"></i> BOOST </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
