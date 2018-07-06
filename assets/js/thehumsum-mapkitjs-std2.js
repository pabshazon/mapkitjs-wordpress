document.addEventListener("DOMContentLoaded", function(event) {

    mapData;
    var urls = [];
    urls[1] = 'http://www.myiconfinder.com/uploads/iconsets/24-24-6096188ce806c80cf30dca727fe7c237.png';
    urls[2] = 'http://www.myiconfinder.com/uploads/iconsets/32-32-6096188ce806c80cf30dca727fe7c237.png';
    urls[3] = 'http://www.myiconfinder.com/uploads/iconsets/48-48-6096188ce806c80cf30dca727fe7c237.png';
    urls[4] = 'http://www.myiconfinder.com/uploads/iconsets/64-64-6096188ce806c80cf30dca727fe7c237.png';
    urls[5] = 'http://www.myiconfinder.com/uploads/iconsets/128-128-6096188ce806c80cf30dca727fe7c237.png';


    mapkit.init({
        authorizationCallback: function(done) {
            done(mapData.jwt);
        }
    });

    var searcher = new mapkit.Search();
    //var geocoder = new mapkit.Geocoder();
    var map = new mapkit.Map("map");
    showAnnotationsByCountry();

    function cleanAnnotationsInMap(){
        map.removeAnnotations(map.annotations);
    }

    function showAnnotationsByCountry(){
        cleanAnnotationsInMap();

        jQuery.each(mapData.data.byCountry, function(i, val) {
            var geocoderPromise = new Promise((resolve, reject) => {
                var latlong = searcher.search(i, (error, data) => {
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
                    },
                };

                var newAnnotation = new mapkit.ImageAnnotation(coordinate, {
                    //color: "#4eabe9",
                    //glyphText: val.total_pledges.toString(),
                    //appearanceAnimation: 'fadeIn',
                    url: {1: urls[5]},
                    size: {
                        width: Math.max(20 * val.total_pledges, 11),
                        height: Math.max(20 * val.total_pledges, 11)
                    },
                    title: i,
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

    }


    function showAnnotationsByZipCode(){
        cleanAnnotationsInMap();
        jQuery.each(mapData.data.byZipCode, function(i, val) {
            //each i is a zipcode, need its latlong too
            //var latlongzip = searcher.search('94117, USA', showGeoResult);

        });
    }

    function formatCurrency(total) {
        var neg = false;
        if(total < 0) {
            neg = true;
            total = Math.abs(total);
        }
        return (neg ? "-$" : '$') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
    }

});