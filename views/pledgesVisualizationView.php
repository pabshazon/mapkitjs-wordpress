<?php
// View for the pledges visualization
function showPledgesVisualizationView($savedMessage = false){
    if($savedMessage){ ?>
        <div class="alert alert-success" role="alert">
            <p><strong>Your pledge have been saved and addded to our statistics, thanks!</strong></p>
        </div>
    <?php } ?>
<section class="row humsum_graph">
    <div class="form-group">
        <select id="filter" name="filter" class="form-control form-control-lg">
            <option value="country">Country</option>
            <option value="zip_code">Zip code</option>
        </select>
    </div>
    <div id="map"></div>
</section>

<section class="row">
    <div class="humsum_graph" id="graph">
        <div class="form-group">
            <select id="filter_vis" name="filter_vis" class="form-control form-control-lg">
                <option value="total">Number of pledges</option>
                <option value="hours">Hours pledged</option>
                <option value="money">Money pledged</option>

            </select>
        </div>
    </div>
</section>
<?php } ?>
