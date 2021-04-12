let selectVille = document.getElementById('event_form_ville');

selectVille.addEventListener('change', function() {
        console.log(this.value);
        let url = "create/location/" + this.value;
        fetch(url, {method: 'GET'})
            .then(function (response) {
                    return response.json();
            }).then( function (data) {
                console.log(data);
        } )

});