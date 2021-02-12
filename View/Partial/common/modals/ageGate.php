<div id="age_gate">
<img src="/Assets/img/logo/NewGDLogo.png" />
<div id="age_gate_container">
    <div id="age_gate_widget">
        <div id="age_checker_header_message">
            <p>You must be 18+ to access this site.</p>
            <p>Please enter your birthday:</p>
        </div>
        <div class="form-group">
            <div id="age_gate_error_message"></div>
        </div>
        <form id="age_gate_form" action="" method="post">
            <div class="row">
                <div class="form-item-day col">
                    <input pattern="[0-9]*" tabindex="1" placeholder="DD" id="age_gate_day" name="day" value="" min="1" max="31" size="2" maxlength="2" class="required numeric form-control" type="number">
                </div><div class="form-item-month col">
                    <input pattern="[0-9]*" tabindex="1" placeholder="MM" id="age_gate_month" name="month" value="" min="1" max="12" size="2" maxlength="2" class="required numeric form-control" type="number">
                </div><div class="form-item form-type-textfield form-item-year col">
                    <input pattern="[0-9]*" tabindex="3" placeholder="YYYY" id="age_gate_year" name="year" value="" min="1900" max="<?=date("Y")-18?>" size="4" maxlength="4" class="required numeric form-control" type="number">
                </div>
            </div>
            <p>You must also consent to our use of cookies. </p>
            <a class="btn btn-active" href="/about/cookies">More Info</a><br/>
            <a class="btn btn-full submit" tabindex="6"><i class="fa fa-check-circle"></i> Submit</a>
            </form>
        </div>
    </div>
</div>
<script>$(function(){
    $("#age_gate").ageGate();
});</script>