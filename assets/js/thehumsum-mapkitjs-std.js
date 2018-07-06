//@todo: refactor this OOP way
document.addEventListener("DOMContentLoaded", function(event) {

    mapData;

    mapkit.init({
        authorizationCallback: function(done) {
            done(mapData.jwt);

        }
    });

    var searcher = new mapkit.Search(); //var geocoder = new mapkit.Geocoder();
    var map = new mapkit.Map("map",{
        showsMapTypeControl: false,
        isZoomEnabled: true,
        isScrollEnabled: true
    });
    var initPoint = new mapkit.Coordinate(42.027803, -47.267823);
    var initSpan = new mapkit.CoordinateSpan(80, 80);
    var initRegion = new mapkit.CoordinateRegion(initPoint, initSpan);

    showAnnotationsByCountry();

    function showAnnotationsByCountry(){
        map.removeAnnotations(map.annotations);

        jQuery.each(mapData.data.byCountry, function(countryName, val) {
            var geocoderPromise = new Promise((resolve, reject) => {
                var latlong = searcher.search(countryName, (error, data) => {
                    resolve(data.places[0].coordinate);

                });

            });

            geocoderPromise.then((data) => {
                var coordinate = new mapkit.Coordinate(data.latitude, data.longitude);
                var calloutDelegate = {
                    calloutContentForAnnotation: function(annotation) {
                        var element = document.createElement("div");
                        element.className = "review-callout-content";

                        var title = element.appendChild(document.createElement("h1"));
                        title.textContent = annotation.title;

                        var totalPledges = element.appendChild(document.createElement("p"));
                        totalPledges.textContent = 'Total Pledges: ' + annotation.data.total_pledges;

                        var hours = element.appendChild(document.createElement("p"));
                        hours.textContent = 'Total Hours: ' + annotation.data.hours + ' h';

                        var money = element.appendChild(document.createElement("p"));
                        money.textContent = 'Total USD Pledged: ' + formatCurrency(annotation.data.money);

                        return element;

                    }
                };

                var newAnnotation = new mapkit.MarkerAnnotation(coordinate, {
                    color: "#4eabe9",
                    glyphText: val.total_pledges.toString(),
                    size: {
                        width: val.total_pledges,
                        height: val.total_pledges
                    },
                    title: countryName,
                    data: {
                        total_pledges : val.total_pledges,
                        hours: val.hours,
                        money: val.money
                    },
                    callout: calloutDelegate
                });

                map.addAnnotation(newAnnotation);

            });
            map.setCenterAnimated(initPoint);
            map.setRegionAnimated(initRegion);

        });

    }

    function showAnnotationsByZipCode(){
        map.removeAnnotations(map.annotations);
        jQuery.each(mapData.data.byZipCode, function(zipcode, val) {
            var geocoderPromise = new Promise((resolve, reject) => {
                var latlong = searcher.search('zipcode ' + zipcode + ', ' + val.country, (error, data) => {
                    resolve(data.places[0].coordinate);

                });

            });

            geocoderPromise.then((data) => {
                var coordinate = new mapkit.Coordinate(data.latitude, data.longitude);
                var calloutDelegate = {
                    calloutContentForAnnotation: function(annotation) {
                        var element = document.createElement("div");
                        element.className = "review-callout-content";

                        var title = element.appendChild(document.createElement("h1"));
                        title.textContent = annotation.title;

                        var totalPledges = element.appendChild(document.createElement("p"));
                        totalPledges.textContent = 'Total Pledges: ' + annotation.data.total_pledges;

                        var hours = element.appendChild(document.createElement("p"));
                        hours.textContent = 'Total Hours: ' + annotation.data.hours + ' h';

                        var money = element.appendChild(document.createElement("p"));
                        money.textContent = 'Total USD Pledged: ' + formatCurrency(annotation.data.money);

                        return element;

                    }
                };

                var newAnnotation = new mapkit.MarkerAnnotation(coordinate, {
                    color: "#4eabe9",
                    glyphText: val.total_pledges.toString(),
                    clusteringIdentifier: 'zipcode',
                    size: {
                        width: val.total_pledges,
                        height: val.total_pledges
                    },
                    title: zipcode,
                    data: {
                        total_pledges : val.total_pledges,
                        hours: val.hours,
                        money: val.money
                    },
                    callout: calloutDelegate
                });

                map.addAnnotation(newAnnotation);

            });

        });
        map.annotationForCluster = function(clusterAnnnotation) {
            if (clusterAnnotation.clusteringIdentifier === "zipcode") {
                clusterAnnotation.title = "Zip codes";
                clusterAnnotation.color = '#A5E8F6';

            }

        };
        map.addEventListener("select", function(event) {
            if ("memberAnnotations" in event.annotation) {
                map.showItems(event.annotation.memberAnnotations);

            }

        });
        //@todo: recenter map to show all points in screen, watch for all promises

    }

    /*
     * Non map events
     */
    jQuery('#filter').on('change', function() {
        var filter = this.value;
        if(filter == 'country'){
            showAnnotationsByCountry();

        }else if(filter == 'zip_code'){
            showAnnotationsByZipCode();

        }
    });

    /*
     * Helper functions
     */
    function formatCurrency(total) {
        var neg = false;
        if(total < 0) {
            neg = true;
            total = Math.abs(total);
        }
        return (neg ? "-$" : '$') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
    }

});