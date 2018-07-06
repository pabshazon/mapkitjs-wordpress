<?php
// View for the pledges input
function showPledgesInputView($adminPostUrl){
?>
<div>
    <form id="pledgesForm" action="<?php echo $adminPostUrl ?>" method="post">
        <input type="hidden" name="action" value="pledge_form">
        <div class="form-group">
            <label for="name">Pledge name</label>
            <input name="name" class="form-control" id="name" type="text" placeholder="Enter pledge name"/>
        </div>
        <div class="form-group">
            <label for="hours">Pledge hours</label>
            <input name="hours" class="form-control" id="hours" type="text" placeholder="Enter pledge hours"/>
            <small id="hoursHelp" class="form-text text-muted">Your pledge may be based in hours, money or both.</small>
        </div>
        <div class="form-group">
            <label for="money">Pledge monetary amount</label>
            <input name="money" class="form-control" id="money" type="text" placeholder="Enter pledge monetary amount"/>
            <small id="moneysHelp" class="form-text text-muted">Your pledge may be based in hours, money or both.</small>
        </div>
        <div class="form-group">
            <label for="country_code">Country</label>
            <select name="country_code" id="country_code" class="form-control bfh-countries" data-country="US"></select>
            <input type="hidden" id="country" name="country" value="">

        </div>
        <div class="form-group">
            <label for="zip_code">Zip code</label>
            <input name="zip_code" class="form-control" id="zip_code" type="text" placeholder="Enter your zipcode"/>
        </div>
        <div class="form-group">
            <label for="description">Pledge description</label>
            <textarea name="description" class="form-control" id="description" type="text" placeholder="Describe your pledge..."></textarea>
        </div>
        <div class="form-group">
            <input class="btn btn-primary" type="submit" name="PledgeSubmit" value="Pledge"/>
        </div>
    </form>
</div>

<script>
    jQuery().ready(function() {
        jQuery( "#pledgesForm" ).submit(function( event ) {
            var countryName = jQuery("#country_code option:selected").text();
            jQuery("#country").val(countryName);

        });

        jQuery("#pledgesForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                country_code: "required",
                zip_code: "required",
                hours: {
                    somethingPledged: ['money', 'hours'],
                    digits: true
                },
                money: {
                    somethingPledged: ['money', 'hours'],
                    digits: true
                }
            },
            messages: {
                name: {
                    required: "Please enter a pledge name",
                    minlength: "Your pladge name must consist of at least 2 characters"
                },
                country_code: "Please select your country",
                zip_code: "Please enter your zip code",
                hours: {
                    somethingPledged: "Your pledge needs either hours or money",
                    digits: "Please enter a number"
                },
                money: {
                    somethingPledged: "Your pledge needs either hours or money",
                    digits: "Please enter a number"
                }
            }
        });

        /**
         * Custom validation to ensure something have been pledged, hours or money.
         * @todo: need to validate they are positive numbers too
         */
        jQuery.validator.addMethod('somethingPledged', function(value, element, params) {
            var field_1 = parseFloat(jQuery('input[name="' + params[0] + '"]').val()),
                field_2 = parseFloat(jQuery('input[name="' + params[1] + '"]').val());
            if(isNaN(field_1)){
                field_1 = 0;
                jQuery('input[name="' + params[0] + '"]').val(field_1);

            }
            if(isNaN(field_2)){
                field_2 = 0;
                jQuery('input[name="' + params[1] + '"]').val(field_2);

            }

            return field_1 + field_2 > 0;

        }, "Your pledge needs either hours or money");

    });

</script>
<?php } ?>
