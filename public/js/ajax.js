let selectVille = document.getElementById('event_form_ville');

selectVille.addEventListener('change', function () {
    console.log(this.value);
    let url = "create/location/" + this.value;
    fetch(url, {method: 'GET'})
        .then(function (response) {
            return response.json();
        }).then(function (data) {

        let selectLocation = document.getElementById('event_form_location');
        removeAllChildren(selectLocation);
        for (const location of data.city) {
            let option = document.createElement("option");
            option.text = location.name;
            option.value = location.id;
            selectLocation.add(option);
        }

        selectLocation.addEventListener('change', function(){
            console.log(data.city); // lieu
            for (const loc of data.city) {
                if (loc.name == this.innerText) {
                    console.log(loc.name);
                }
            }
        } )

        let selectStreet = document.getElementById('event_form_street');
        let selectLatitude = document.getElementById('event_form_latitude');
        let selectLongitude = document.getElementById('event_form_longitude');


        let selectPostCode = document.getElementById('event_form_postcode');
        removeAllChildren(selectPostCode);
        let optionPostCode = document.createElement("option");
        optionPostCode.text = data.postCode;
        selectPostCode.add(optionPostCode);

    })

});

function removeAllChildren(select) {
    while (select.lastElementChild) {
        select.removeChild(select.lastElementChild);
    }
}
